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
use App\Mail\PedidoPagadoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

    /**
     * 🔥 CREAR PREFERENCIA - CON TODOS LOS CAMPOS RECOMENDADOS POR MERCADOPAGO
     */
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
                
<<<<<<< HEAD
                // ✅ CAMBIOS IMPORTANTES: Agregar todos los campos recomendados
                $items[] = [
                    "id" => $producto->codigo,                          // ✅ Código del item (recomendado)
                    "title" => $producto->nombre,                       // ✅ Nombre del item (recomendado)
                    "description" => $producto->nombre . " - " . $producto->litros . " litros", // ✅ Descripción (recomendado)
                    "quantity" => (int) $cantidad,                      // ✅ Cantidad (recomendado)
                    "unit_price" => (float) $precio,                    // ✅ Precio unitario (recomendado)
                    "currency_id" => "MXN",
                    "category_id" => $this->getCategoryId($producto->categoria_id ?? 1), // ✅ Categoría (recomendado)
                    "picture_url" => asset('assets/img/productos/' . $producto->codigo . '.jpg'), // Imagen opcional
=======
                $items[] = [
                    "title" => $producto->nombre,
                    "quantity" => (int) $cantidad,
                    "unit_price" => (float) $precio,
                    "currency_id" => "MXN"
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
                ];
            }

            $emailCliente = auth()->user()->email ?? $checkoutData['datos']['email'] ?? 'cliente@tanquestlaloc.com';
<<<<<<< HEAD
            
            // ✅ AGREGAR SHIPMENTS (envío) si aplica
            $shipments = [];
            if (isset($checkoutData['cobertura']['distancia']) && $checkoutData['cobertura']['distancia'] > 0) {
                $costoEnvio = $this->calcularCostoEnvio($checkoutData['cobertura']['distancia']);
                if ($costoEnvio > 0) {
                    $shipments = [
                        "cost" => $costoEnvio,
                        "mode" => "not_specified",
                    ];
                    // Agregar item de envío
                    $items[] = [
                        "id" => "ENVIO",
                        "title" => "Costo de Envío",
                        "description" => "Envío a domicilio",
                        "quantity" => 1,
                        "unit_price" => (float) $costoEnvio,
                        "currency_id" => "MXN",
                        "category_id" => "services",
                    ];
                }
            }
=======
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
    
            $preferenceData = [
                "items" => $items,
                "payer" => [
                    "email" => $emailCliente,
                    "name" => $checkoutData['datos']['nombre'] ?? 'Cliente',
<<<<<<< HEAD
                    "surname" => "",
                    "phone" => [
                        "number" => $checkoutData['datos']['telefono'] ?? '',
                        "area_code" => ""
                    ],
                    "address" => [
                        "zip_code" => $checkoutData['datos']['codigo_postal'] ?? '00000',
                        "street_name" => $checkoutData['datos']['direccion'] ?? '',
                        "street_number" => ""
                    ]
                ],
                "shipments" => $shipments,
=======
                ],
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
                "back_urls" => [
                    "success" => route('pago.success'),
                    "failure" => route('pago.failure'),
                    "pending" => route('pago.pending')
                ],
<<<<<<< HEAD
                "auto_return" => "approved",
                "external_reference" => $folio,
                "statement_descriptor" => "TANQUES TLALOC",
                "expires" => true,
                "expiration_date_to" => date('c', strtotime('+1 day')),
                // ✅ NOTIFICACIONES: importante para webhook
                "notification_url" => route('pago.webhook'),
                // ✅ METADATOS adicionales
                "metadata" => [
                    "folio" => $folio,
                    "cliente_id" => $checkoutData['cliente_id'] ?? null,
                    "sucursal_id" => $checkoutData['cobertura']['sucursal_id'] ?? null
                ]
=======
                "external_reference" => $folio,
                "statement_descriptor" => "TANQUES TLALOC",
                "expires" => true,
                "expiration_date_to" => date('c', strtotime('+1 day'))
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
            ];

            $preference = $client->create($preferenceData);

<<<<<<< HEAD
            Log::info('Preferencia creada correctamente', [
                'preference_id' => $preference->id,
                'folio' => $folio,
                'items_count' => count($items)
            ]);

            return ['id' => $preference->id];

        } catch (MPApiException $e) {
            Log::error('Error API en crearPreferencia:', [
                'folio' => $folio, 
                'message' => $e->getMessage(),
                'response' => $e->getApiResponse() ? $e->getApiResponse()->getContent() : null
            ]);
=======
            return ['id' => $preference->id];

        } catch (MPApiException $e) {
            Log::error('Error API en crearPreferencia:', ['folio' => $folio, 'message' => $e->getMessage()]);
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error general en crearPreferencia:', ['folio' => $folio, 'message' => $e->getMessage()]);
            throw $e;
        }
<<<<<<< HEAD
    }

    /**
     * Mapear categoría de producto a las categorías aceptadas por MercadoPago
     */
    private function getCategoryId($categoriaId)
    {
        // Categorías aceptadas por MercadoPago:
        // "home_appliances", "electronics", "furniture", "toys", 
        // "games", "musical_instruments", "sports", "collectibles", 
        // "art", "baby", "mobile", "computing", "cameras", "other"
        
        $categories = [
            1 => "home_appliances",      // Tinacos
            2 => "home_appliances",      // Tinacos Bala
            3 => "home_appliances",      // Cisternas
            4 => "home_appliances",      // Accesorios
        ];
        
        return $categories[$categoriaId] ?? "home_appliances";
    }

    /**
     * Calcular costo de envío basado en distancia (opcional)
     */
    private function calcularCostoEnvio($distanciaKm)
    {
        if ($distanciaKm <= 5) {
            return 0; // Envío gratis dentro de 5km
        } elseif ($distanciaKm <= 15) {
            return 150;
        } elseif ($distanciaKm <= 30) {
            return 250;
        } else {
            return 400;
        }
=======
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
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

            Log::info('📊 Respuesta COMPLETA de MercadoPago:', [
                'payment_id' => $paymentId,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'external_reference' => $payment->external_reference ?? null,
                'payment_method_id' => $payment->payment_method_id ?? null,
                'payment_type_id' => $payment->payment_type_id ?? null,
                'transaction_amount' => $payment->transaction_amount ?? null,
                'installments' => $payment->installments ?? null,
                'issuer_id' => $payment->issuer_id ?? null,
                'operation_type' => $payment->operation_type ?? null,
                'date_created' => $payment->date_created ?? null,
                'date_approved' => $payment->date_approved ?? null,
            ]);

            if ($payment->status === 'rejected') {
                Log::error('🔴🔴🔴 PAGO RECHAZADO - MOTIVO DETALLADO 🔴🔴🔴', [
                    'payment_id' => $paymentId,
                    'status_detail' => $payment->status_detail,
                    'description' => $payment->description ?? 'Sin descripción',
                    'payment_method_id' => $payment->payment_method_id,
                    'card_holder_name' => $payment->card->cardholder->name ?? null,
                    'card_first_six' => $payment->card->first_six_digits ?? null,
                    'card_last_four' => $payment->card->last_four_digits ?? null,
                    'issuer_id' => $payment->issuer_id ?? null,
                    'authorization_code' => $payment->authorization_code ?? null,
                    'error_message' => $payment->error_message ?? null,
                    'refusal_reason' => $payment->refusal_reason ?? null,
                ]);

                $rejectReasons = [
                    'cc_rejected_bad_filled_card_number' => '❌ Número de tarjeta incorrecto',
                    'cc_rejected_bad_filled_date' => '❌ Fecha de expiración incorrecta',
                    'cc_rejected_bad_filled_other' => '❌ Datos de la tarjeta incorrectos',
                    'cc_rejected_bad_filled_security_code' => '❌ Código de seguridad (CVV) incorrecto',
                    'cc_rejected_blacklist' => '🚫 Tarjeta en lista negra',
                    'cc_rejected_call_for_authorize' => '📞 El banco requiere autorización, llama al banco',
                    'cc_rejected_card_disabled' => '🔒 Tarjeta deshabilitada',
                    'cc_rejected_duplicated_payment' => '🔄 Pago duplicado',
                    'cc_rejected_high_risk' => '⚠️ Operación de alto riesgo',
                    'cc_rejected_insufficient_amount' => '💰 Saldo insuficiente en la tarjeta',
                    'cc_rejected_invalid_installments' => '📅 Número de cuotas inválido',
                    'cc_rejected_max_attempts' => '🔄 Demasiados intentos, tarjeta bloqueada',
                    'cc_rejected_other_reason' => '❓ Otra razón, contacta al banco',
                    'cc_rejected_card_type_not_allowed' => '🚫 Tipo de tarjeta no permitida',
                    'cc_rejected_credit_line' => '💳 Línea de crédito insuficiente',
                    'cc_rejected_temporary_error' => '⏳ Error temporal, intenta de nuevo'
                ];

                $reason = $rejectReasons[$payment->status_detail] ?? '❓ Razón desconocida: ' . $payment->status_detail;
                Log::error('🔴 MOTIVO LEGIBLE DEL RECHAZO: ' . $reason);
            }

            if ($payment->status === 'approved') {
                
                $folio = $payment->external_reference;
                
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
                        
                        if (isset($checkoutData['cobertura']['sucursal_id'])) {
                            DB::table('producto_sucursal')
                                ->where('producto_id', $id)
                                ->where('sucursal_id', $checkoutData['cobertura']['sucursal_id'])
                                ->decrement('existencias', $cantidad);
                            Log::info('📦 Stock descontado', [
                                'producto_id' => $id, 
                                'cantidad' => $cantidad,
                                'sucursal_id' => $checkoutData['cobertura']['sucursal_id']
                            ]);
                        } else {
                            Log::warning('⚠️ No se pudo descontar stock: sucursal_id no encontrado');
                        }
                    }
                }

                DB::commit();
                
                // ❌ CORREO ELIMINADO - Ya no se envía aquí
                
                $pagoPendiente->status = 'procesado';
                $pagoPendiente->mp_payment_id = $paymentId;
                $pagoPendiente->save();
                
                Log::info('=== INICIANDO LIMPIEZA DE CARRITO ===');
                $carritoAntes = CarritoHelper::getCarrito();
                Log::info('Carrito ANTES de vaciar:', ['carrito' => $carritoAntes]);
                
                CarritoHelper::vaciar();
                session()->forget('checkout_data');
                
                $carritoDespues = CarritoHelper::getCarrito();
                Log::info('Carrito DESPUÉS de vaciar:', ['carrito' => $carritoDespues]);
                
                Log::info('✅ Pago procesado exitosamente', ['folio' => $folio]);
                
            } else {
                Log::info('Pago no aprobado', ['payment_id' => $paymentId, 'status' => $payment->status]);
                
                $folio = $payment->external_reference;
                
                if (!$folio) {
                    $pagoPendiente = PagoPendiente::where('mp_payment_id', $paymentId)
                                        ->where('status', 'pendiente')
                                        ->first();
                    if ($pagoPendiente) {
                        $folio = $pagoPendiente->folio;
                    }
                }
                
                if ($folio) {
                    $pagoPendiente = PagoPendiente::where('folio', $folio)
                                        ->where('status', 'pendiente')
                                        ->first();
                    
                    if ($pagoPendiente) {
                        $pagoPendiente->mp_payment_id = $paymentId;
                        $pagoPendiente->status = 'rechazado';
                        $pagoPendiente->save();
                        Log::info('✅ Pago rechazado registrado', ['folio' => $folio, 'payment_id' => $paymentId]);
                    }
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error en procesarPago:', [
                'payment_id' => $paymentId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

        // ============================================================
        // ===== ENVIAR CORREO AL CLIENTE (SOLO AQUÍ, UNA VEZ) =====
        // ============================================================
        if ($pedido && $pedido->cliente && $pedido->cliente->email) {
            try {
                Mail::to($pedido->cliente->email)->send(new PedidoPagadoMail($pedido, $pedido->cliente));
                Log::info('✅ Correo de pago confirmado enviado a: ' . $pedido->cliente->email);
            } catch (\Exception $e) {
                Log::error('❌ Error al enviar correo: ' . $e->getMessage());
            }
        }
        // ============================================================

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
<<<<<<< HEAD
     * 🔥 PROCESAR PAGO DESDE EL BRICK - Con campos mejorados
=======
     * 🔥 PROCESAR PAGO DESDE EL BRICK
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
     */
    public function processPayment(Request $request)
    {
        $folio = $request->input('folio');
        $token = $request->input('token');
        $paymentMethodId = $request->input('paymentMethodId');
        $installments = $request->input('installments', 1);
        $issuerId = $request->input('issuerId');
        
        Log::info('processPayment llamado - PRODUCCION', [
            'folio' => $folio,
            'amount' => $request->input('amount'),
            'payment_method_id' => $paymentMethodId,
            'installments' => $installments
        ]);
        
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
        
        Log::info('🔍 DATOS COMPLETOS DEL CLIENTE:', [
            'folio' => $folio,
            'email' => auth()->guard('cliente')->user()->email ?? $checkoutData['datos']['email'] ?? null,
            'nombre' => $checkoutData['datos']['nombre'] ?? null,
            'telefono' => $checkoutData['datos']['telefono'] ?? null,
            'direccion' => $checkoutData['datos']['direccion'] ?? null,
            'ciudad' => $checkoutData['datos']['ciudad'] ?? null,
            'codigo_postal' => $checkoutData['datos']['codigo_postal'] ?? null
        ]);
        
        try {
            $client = new PaymentClient();
            
            $emailCliente = null;
            
            if (auth()->guard('cliente')->check()) {
                $emailCliente = auth()->guard('cliente')->user()->email;
            } elseif (!empty($checkoutData['datos']['email'])) {
                $emailCliente = $checkoutData['datos']['email'];
            } else {
                $telefono = $checkoutData['datos']['telefono'] ?? 'cliente';
                $emailCliente = $telefono . '@tanquestlaloc.com';
            }
            
<<<<<<< HEAD
            // ✅ CONSTRUIR DESCRIPCIÓN COMPLETA DE LOS ITEMS
            $descripcionItems = "";
            $totalItems = 0;
            foreach ($checkoutData['carrito'] as $id => $item) {
                $producto = Producto::find($id);
                if ($producto) {
                    $cantidad = is_array($item) ? $item['cantidad'] : $item;
                    $descripcionItems .= ($descripcionItems ? ", " : "") . $producto->nombre . " x" . $cantidad;
                    $totalItems += $cantidad;
                }
            }
            
            $paymentData = [
                "transaction_amount" => (float) $request->input('amount'),
                "token" => $token,
                "description" => "Pedido " . $folio . " - " . $descripcionItems,  // ✅ Descripción completa
=======
            $paymentData = [
                "transaction_amount" => (float) $request->input('amount'),
                "token" => $token,
                "description" => "Pedido " . $folio,
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
                "installments" => (int) $installments,
                "payment_method_id" => $paymentMethodId,
                "external_reference" => $folio,
                "payer" => [
                    "email" => $emailCliente,
                    "first_name" => $checkoutData['datos']['nombre'] ?? 'Cliente',
                    "last_name" => "",
                    "phone" => [
<<<<<<< HEAD
                        "number" => $checkoutData['datos']['telefono'] ?? null,
                        "area_code" => ""
                    ],
                    "address" => [
                        "zip_code" => $checkoutData['datos']['codigo_postal'] ?? '00000',
                        "street_name" => $checkoutData['datos']['direccion'] ?? null,
                        "street_number" => ""
                    ]
                ],
                // ✅ METADATOS ADICIONALES para mejorar aprobación
                "additional_info" => [
                    "items" => $this->getItemsForAdditionalInfo($checkoutData['carrito']),
                    "payer" => [
                        "first_name" => $checkoutData['datos']['nombre'] ?? 'Cliente',
                        "phone" => [
                            "number" => $checkoutData['datos']['telefono'] ?? null
                        ],
                        "address" => [
                            "zip_code" => $checkoutData['datos']['codigo_postal'] ?? '00000',
                            "street_name" => $checkoutData['datos']['direccion'] ?? null
                        ]
                    ],
                    "shipments" => [
                        "receiver_address" => [
                            "zip_code" => $checkoutData['datos']['codigo_postal'] ?? '00000',
                            "street_name" => $checkoutData['datos']['direccion'] ?? null,
                            "city_name" => $checkoutData['datos']['ciudad'] ?? null,
                            "state_name" => $checkoutData['datos']['estado'] ?? null
                        ]
=======
                        "number" => $checkoutData['datos']['telefono'] ?? null
                    ],
                    "address" => [
                        "zip_code" => $checkoutData['datos']['codigo_postal'] ?? null,
                        "street_name" => $checkoutData['datos']['direccion'] ?? null
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
                    ]
                ]
            ];
            
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
                
<<<<<<< HEAD
=======
                // ❌ CORREO ELIMINADO - Ya no se envía aquí
                
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
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

    /**
     * Obtener items formateados para additional_info
     */
    private function getItemsForAdditionalInfo($carrito)
    {
        $items = [];
        foreach ($carrito as $id => $item) {
            $producto = Producto::find($id);
            if ($producto) {
                $cantidad = is_array($item) ? $item['cantidad'] : $item;
                $precio = is_array($item) ? $item['precio'] : $producto->precio;
                
                $items[] = [
                    "id" => $producto->codigo,
                    "title" => $producto->nombre,
                    "description" => $producto->nombre . " - " . $producto->litros . " litros",
                    "quantity" => (int) $cantidad,
                    "unit_price" => (float) $precio,
                    "category_id" => $this->getCategoryId($producto->categoria_id ?? 1)
                ];
            }
        }
        return $items;
    }
}