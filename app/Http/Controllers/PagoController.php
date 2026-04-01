<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\PagoPendiente;
use App\Helpers\CarritoHelper;
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
    private $secretKey;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token');
        $this->publicKey = config('services.mercadopago.public_key');
        $this->secretKey = config('services.mercadopago.secret');
        
        Log::info('PagoController inicializado - PRODUCCION', [
            'access_token_exists' => !empty($this->accessToken),
            'public_key_exists' => !empty($this->publicKey),
            'secret_key_exists' => !empty($this->secretKey)
        ]);
        
        MercadoPagoConfig::setAccessToken($this->accessToken);
    }

    public function index(Request $request)
    {
        $folio = $request->get('folio');
        
        if (!$folio) {
            return redirect()->route('tienda')->with('error', 'No hay datos de checkout');
        }
        
        $pagoPendiente = PagoPendiente::where('folio', $folio)
                            ->where('status', 'pendiente')
                            ->first();
        
        if (!$pagoPendiente) {
            return redirect()->route('tienda')->with('error', 'Datos de checkout no encontrados');
        }
        
        $checkoutData = $pagoPendiente->checkout_data;
        
        try {
            $preferenceData = $this->crearPreferencia($checkoutData, $folio);
            
            $pagoPendiente->mp_preference_id = $preferenceData['id'];
            $pagoPendiente->save();
            
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
                'publicKey' => $this->publicKey
            ]);

        } catch (MPApiException $e) {
            Log::error('Error API MercadoPago en index:', ['folio' => $folio, 'message' => $e->getMessage()]);
            return redirect()->route('cliente.dashboard')->with('swal', [
                'type' => 'error',
                'title' => 'Error de pago',
                'message' => 'Error al conectar con Mercado Pago. Intenta nuevamente.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error general en index:', ['folio' => $folio, 'message' => $e->getMessage()]);
            return redirect()->route('cliente.dashboard')->with('swal', [
                'type' => 'error',
                'title' => 'Error de pago',
                'message' => 'Error al procesar el pago. Intenta nuevamente.'
            ]);
        }
    }

    private function crearPreferencia($checkoutData, $folio)
    {
        try {
            $client = new PreferenceClient();
            
            $items = [];
            foreach ($checkoutData['carrito'] as $id => $item) {
                $producto = Producto::find($id);
                
                if (!$producto) {
                    throw new \Exception("Producto con ID {$id} no encontrado");
                }
                
                $precio = is_array($item) ? $item['precio'] : $producto->precio;
                $cantidad = is_array($item) ? $item['cantidad'] : $item;
                
                $items[] = [
                    "title" => $producto->nombre,
                    "quantity" => (int) $cantidad,
                    "unit_price" => (float) $precio,
                    "currency_id" => "MXN"
                ];
            }

            $emailCliente = auth()->user()->email ?? $checkoutData['datos']['email'] ?? 'cliente@tanquestlaloc.com';
    
            $preferenceData = [
                "items" => $items,
                "payer" => [
                    "email" => $emailCliente,
                    "name" => $checkoutData['datos']['nombre'] ?? 'Cliente',
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
            ];

            $preference = $client->create($preferenceData);

            return ['id' => $preference->id];

        } catch (MPApiException $e) {
            Log::error('Error API en crearPreferencia:', ['folio' => $folio, 'message' => $e->getMessage()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error general en crearPreferencia:', ['folio' => $folio, 'message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function webhook(Request $request)
    {
        $data = $request->all();
        
        Log::info('Webhook recibido:', ['data' => $data]);

        if (isset($data['data']['id']) && $data['data']['id'] == "123456") {
            Log::info('✅ Webhook de prueba recibido');
            return response()->json(['message' => 'Test OK'], 200);
        }

        if (!isset($data['type']) || !isset($data['data']['id'])) {
            Log::warning('Webhook: Datos inválidos');
            return response()->json(['error' => 'Datos inválidos'], 400);
        }

        if ($data['type'] === 'payment') {
            $paymentId = $data['data']['id'];
            Log::info('Webhook: Procesando pago', ['payment_id' => $paymentId]);
            $this->procesarPago($paymentId);
            return response()->json(['message' => 'Recibido'], 200);
        }

        return response()->json(['message' => 'Evento no manejado'], 200);
    }

    private function procesarPago($paymentId)
    {
        try {
            $client = new PaymentClient();
            $payment = $client->get((int)$paymentId);

            Log::info('Respuesta de MercadoPago:', [
                'payment_id' => $paymentId,
                'status' => $payment->status,
                'external_reference' => $payment->external_reference ?? null
            ]);

            if ($payment->status === 'approved') {
                
                $folio = $payment->external_reference;
                
                // 👈 SI NO VIENE external_reference, BUSCAR POR mp_payment_id
                if (!$folio) {
                    Log::warning('Pago sin external_reference, buscando por payment_id');
                    $pagoPendiente = PagoPendiente::where('mp_payment_id', $paymentId)
                                        ->where('status', 'pendiente')
                                        ->first();
                    if ($pagoPendiente) {
                        $folio = $pagoPendiente->folio;
                        Log::info('✅ Folio recuperado por payment_id', ['folio' => $folio]);
                    }
                }
                
                if (!$folio) {
                    Log::error('No se pudo recuperar el folio para el pago', ['payment_id' => $paymentId]);
                    return;
                }
                
                $pagoPendiente = PagoPendiente::where('folio', $folio)
                                    ->where('status', 'pendiente')
                                    ->first();
                
                if (!$pagoPendiente) {
                    Log::error('No hay pago pendiente para folio', ['folio' => $folio]);
                    return;
                }
                
                $existe = Pedido::where('folio', $folio)->first();
                if ($existe) {
                    Log::warning('Pedido ya existe', ['folio' => $folio]);
                    return;
                }
                
                $checkoutData = $pagoPendiente->checkout_data;
                
                DB::beginTransaction();
                
                $pedido = Pedido::create([
                    'cliente_id' => $checkoutData['cliente_id'],
                    'folio' => $folio,
                    'cliente_nombre' => $checkoutData['datos']['nombre'] ?? '',
                    'cliente_telefono' => $checkoutData['datos']['telefono'] ?? '',
                    'cliente_direccion' => $checkoutData['datos']['direccion'] ?? '',
                    'cliente_ciudad' => $checkoutData['datos']['ciudad'] ?? '',
                    'cliente_estado' => $checkoutData['datos']['estado'] ?? '',
                    'codigo_postal' => $checkoutData['datos']['codigo_postal'] ?? '',
                    'total' => $checkoutData['total'] ?? 0,
                    'metodo_pago' => 'mercadopago',
                    'pago_confirmado' => true,
                    'estado' => 'confirmado',
                    'notas' => $checkoutData['datos']['notas'] ?? null,
                    'sucursal_id' => $checkoutData['cobertura']['sucursal_id'] ?? null,
                    'distancia_km' => $checkoutData['cobertura']['distancia'] ?? 0,
                    'cobertura_verificada' => true,
                    'mp_payment_id' => $paymentId,
                    'mp_status' => $payment->status,
                    'mp_status_detail' => $payment->status_detail ?? null,
                    'mp_response' => json_encode($payment)
                ]);

                Log::info('Pedido creado', ['pedido_id' => $pedido->id, 'folio' => $folio]);

                foreach ($checkoutData['carrito'] as $id => $item) {
                    $producto = Producto::find($id);
                    
                    if ($producto) {
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
                }

                DB::commit();
                
                $pagoPendiente->status = 'procesado';
                $pagoPendiente->save();
                
                CarritoHelper::vaciar();
                session()->forget('checkout_data');
                
                Log::info('✅ Pago procesado exitosamente', ['folio' => $folio]);
                
            } else {
                Log::info('Pago no aprobado', ['payment_id' => $paymentId, 'status' => $payment->status]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en procesarPago:', [
                'payment_id' => $paymentId,
                'message' => $e->getMessage()
            ]);
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
        Log::warning('Pago fallido', ['params' => $request->all()]);
        return view('pago.failure', [
            'message' => 'El pago no pudo ser procesado. Intenta nuevamente.'
        ]);
    }

    public function pending(Request $request)
    {
        Log::info('Pago pendiente', ['params' => $request->all()]);
        return view('pago.pending', [
            'message' => 'Tu pago está siendo procesado. Te notificaremos cuando se confirme.'
        ]);
    }

    /**
     * 🔥 PROCESAR PAGO DESDE EL BRICK - CORREGIDO CON external_reference
     */
    public function processPayment(Request $request)
    {
        Log::info('processPayment llamado - PRODUCCION', [
            'folio' => $request->input('folio'),
            'amount' => $request->input('amount'),
            'preference_id' => $request->input('preference_id'),
            'payment_method_id' => $request->input('paymentMethodId')
        ]);

        $folio = $request->input('folio');
        $token = $request->input('token');
        $paymentMethodId = $request->input('paymentMethodId');
        $installments = $request->input('installments', 1);
        $issuerId = $request->input('issuerId');
        
        if (!$folio) {
            return response()->json([
                'success' => false,
                'message' => 'No se proporcionó folio'
            ], 400);
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'No se proporcionó token de pago'
            ], 400);
        }

        $pagoPendiente = PagoPendiente::where('folio', $folio)
                            ->where('status', 'pendiente')
                            ->first();

        if (!$pagoPendiente) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el pedido pendiente'
            ], 404);
        }

        $checkoutData = $pagoPendiente->checkout_data;
        
        try {
            $client = new PaymentClient();
            
            $paymentData = [
                "transaction_amount" => (float) $request->input('amount'),
                "token" => $token,
                "description" => "Pedido " . $folio,
                "installments" => (int) $installments,
                "payment_method_id" => $paymentMethodId,
                "external_reference" => $folio, // 👈 ESTA ES LA LÍNEA CRÍTICA QUE FALTABA
                "payer" => [
                    "email" => auth()->user()->email ?? $checkoutData['datos']['email'] ?? 'cliente@tanquestlaloc.com',
                ]
            ];
            
            // 👈 SOLO AGREGAR issuer_id SI VIENE Y ES VÁLIDO
            if ($issuerId && is_numeric($issuerId) && $issuerId > 0) {
                $paymentData["issuer_id"] = (int) $issuerId;
                Log::info('Agregando issuer_id', ['issuer_id' => $issuerId]);
            }
            
            Log::info('Enviando pago a MercadoPago', ['data' => $paymentData]);
            
            $payment = $client->create($paymentData);
            
            Log::info('Pago creado en MercadoPago', [
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'folio' => $folio,
                'external_reference' => $folio
            ]);
            
            if ($payment->status === 'approved') {
                $pagoPendiente->mp_payment_id = $payment->id;
                $pagoPendiente->save();
                
                return response()->json([
                    'success' => true,
                    'payment_id' => $payment->id,
                    'status' => $payment->status,
                    'folio' => $folio,
                    'message' => 'Pago aprobado'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Pago no aprobado: ' . $payment->status,
                    'status' => $payment->status
                ], 400);
            }

        } catch (MPApiException $e) {
            $apiResponse = $e->getApiResponse() ? $e->getApiResponse()->getContent() : 'No response';
            Log::error('Error API MercadoPago en processPayment:', [
                'folio' => $folio,
                'message' => $e->getMessage(),
                'response' => $apiResponse
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error en processPayment:', [
                'folio' => $folio,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }
}