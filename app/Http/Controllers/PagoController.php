<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
{
    private $accessToken;
    private $publicKey;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token');
        $this->publicKey = config('services.mercadopago.public_key');
        
        MercadoPagoConfig::setAccessToken($this->accessToken);
    }

    public function index()
    {
        if (!session()->has('checkout_data')) {
            return redirect()->route('tienda')->with('error', 'No hay datos de checkout');
        }

        $checkoutData = session('checkout_data');
        
        try {
            $folio = 'PED-' . date('ymd') . '-' . rand(1000, 9999);
            
            $preferenceData = $this->crearPreferencia($checkoutData, $folio);
            
            $pedidoArray = [
                'folio' => $folio,
                'total' => $checkoutData['total'],
                'sucursal' => [
                    'nombre' => $checkoutData['cobertura']['sucursal_nombre'] ?? 'No disponible',
                    'distancia' => $checkoutData['cobertura']['distancia'] ?? 0
                ]
            ];
            
            return view('cliente.pago', [
                'pedido' => $pedidoArray,
                'preferenceId' => $preferenceData['id'],
                'publicKey' => $this->publicKey,
                'sucursal' => $checkoutData['cobertura'] ?? null
            ]);

        } catch (MPApiException $e) {
            Log::error('Error API MP:', [
                'message' => $e->getMessage(),
                'response' => $e->getApiResponse() ? $e->getApiResponse()->getContent() : 'No response'
            ]);
            
            return redirect()->route('cliente.dashboard')->with('swal', [
                'type' => 'error',
                'title' => 'Error de pago',
                'message' => 'Error al conectar con Mercado Pago. Intenta nuevamente.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error general: ' . $e->getMessage());
            
            return redirect()->route('cliente.dashboard')->with('swal', [
                'type' => 'error',
                'title' => 'Error de pago',
                'message' => 'Error al procesar el pago. Intenta nuevamente.'
            ]);
        }
    }

    private function crearPreferencia($checkoutData, $folio)
    {
        $client = new PreferenceClient();
        
        $items = [];
        foreach ($checkoutData['carrito'] as $id => $item) {
            $producto = Producto::find($id);
            $precio = is_array($item) ? $item['precio'] : $producto->precio;
            $cantidad = is_array($item) ? $item['cantidad'] : $item;
            
            $items[] = [
                "title" => $producto->nombre,
                "quantity" => (int) $cantidad,
                "unit_price" => (float) $precio,
                "currency_id" => "MXN"
            ];
        }

        $emailPrueba = 'test_user_123456@testuser.com';

        $preference = $client->create([
            "items" => $items,
            "payer" => [
                "email" => $emailPrueba,
                "name" => $checkoutData['datos']['nombre'],
            ],
            "back_urls" => [
                "success" => route('pago.success'),
                "failure" => route('pago.failure'),
                "pending" => route('pago.pending')
            ],
            "external_reference" => $folio,
            "statement_descriptor" => "TANQUES TLALOC",
            "expires" => true,
            "expiration_date_to" => date('c', strtotime('+1 day'))
        ]);

        return [
            'id' => $preference->id
        ];
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        Log::info('Webhook recibido:', $data);

        if (!isset($data['type']) || !isset($data['data']['id'])) {
            return response()->json(['error' => 'Datos inválidos'], 400);
        }

        if ($data['type'] === 'payment') {
            $paymentId = $data['data']['id'];
            $this->procesarPago($paymentId);
        }

        return response()->json(['message' => 'OK']);
    }

    private function procesarPago($paymentId)
    {
        try {
            $client = new PaymentClient();
            $payment = $client->get($paymentId);

            if ($payment->status === 'approved') {
                
                $checkoutData = session('checkout_data');
                
                if (!$checkoutData) {
                    Log::error('No hay datos de checkout');
                    return;
                }
                
                DB::beginTransaction();
                
                $folio = $payment->external_reference;
                
                $pedido = Pedido::create([
                    'cliente_id' => $checkoutData['cliente_id'],
                    'folio' => $folio,
                    'cliente_nombre' => $checkoutData['datos']['nombre'],
                    'cliente_telefono' => $checkoutData['datos']['telefono'],
                    'cliente_direccion' => $checkoutData['datos']['direccion'],
                    'cliente_ciudad' => $checkoutData['datos']['ciudad'],
                    'cliente_estado' => $checkoutData['datos']['estado'],
                    'codigo_postal' => $checkoutData['datos']['codigo_postal'],
                    'total' => $checkoutData['total'],
                    'metodo_pago' => 'mercadopago',
                    'pago_confirmado' => true,
                    'estado' => 'confirmado',
                    'notas' => $checkoutData['datos']['notas'] ?? null,
                    'sucursal_id' => $checkoutData['cobertura']['sucursal_id'],
                    'distancia_km' => $checkoutData['cobertura']['distancia'],
                    'cobertura_verificada' => true,
                    'mp_payment_id' => $paymentId,
                    'mp_status' => $payment->status,
                    'mp_status_detail' => $payment->status_detail,
                    'mp_response' => json_encode($payment)
                ]);

                foreach ($checkoutData['carrito'] as $id => $item) {
                    $producto = Producto::find($id);
                    $precio = is_array($item) ? $item['precio'] : $producto->precio;
                    $cantidad = is_array($item) ? $item['cantidad'] : $item;
                    
                    PedidoItem::create([
                        'pedido_id' => $pedido->id,
                        'producto_id' => $id,
                        'producto_nombre' => $producto->nombre,
                        'cantidad' => $cantidad,
                        'precio' => $precio
                    ]);
                }

                DB::commit();
                
                session()->forget('checkout_data');
                session()->forget('carrito');
                
                Log::info('Pedido creado: ' . $pedido->folio);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando pedido: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $externalRef = $request->get('external_reference');

        if ($paymentId) {
            $this->procesarPago($paymentId);
        }

        $pedido = Pedido::where('folio', $externalRef)->first();

        return view('pago.success', [
            'pedido' => $pedido,
            'paymentId' => $paymentId
        ]);
    }

    public function failure(Request $request)
    {
        return view('pago.failure', [
            'message' => 'El pago no pudo ser procesado. Intenta nuevamente.'
        ]);
    }

    public function pending(Request $request)
    {
        return view('pago.pending', [
            'message' => 'Tu pago está siendo procesado. Te notificaremos cuando se confirme.'
        ]);
    }
}