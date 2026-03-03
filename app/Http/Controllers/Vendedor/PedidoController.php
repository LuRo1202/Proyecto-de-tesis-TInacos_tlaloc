<?php
namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PedidoResponsable;
use App\Models\PedidoHistorial;
use App\Models\Producto;
use App\Models\Cliente;
use Carbon\Carbon;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }
        
        $sucursalNombre = $sucursal->nombre;
        $sucursal_id = $sucursal->id;

        $verDisponibles = $request->has('disponibles') && $request->disponibles == 1;
        $estado = $request->estado ?? 'todos';
        $desde = $request->desde;
        $hasta = $request->hasta;

        $pedidos = $this->getPedidos($usuario_id, $sucursal_id, $verDisponibles, $estado, $desde, $hasta);
        $contadores = $this->getContadores($usuario_id, $sucursal_id);
        $totalDisponibles = $this->getTotalDisponibles($sucursal_id);

        return view('vendedor.pedidos.index', compact(
            'pedidos',
            'contadores',
            'totalDisponibles',
            'verDisponibles',
            'estado',
            'desde',
            'hasta',
            'sucursal',
            'sucursalNombre',
            'usuario'
        ));
    }

    /**
     * Show the form for creating a new order
     */
    public function create(Request $request)
    {
        $usuario = Auth::user();
        
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }
        
        // Obtener productos con existencias
        $productos = Producto::where('activo', 1)
            ->with(['sucursales' => function($query) use ($sucursal) {
                $query->where('sucursal_id', $sucursal->id)
                    ->select('producto_sucursal.existencias');
            }])
            ->orderBy('nombre')
            ->get();
        
        foreach ($productos as $producto) {
            $producto->existencias = $producto->sucursales->first()->pivot->existencias ?? 0;
        }
        
        // Variables para precarga desde URL
        $producto_id = $request->get('producto_id');
        $cantidad = $request->get('cantidad', 1);
        $producto_precargado = null;
        $precio_con_oferta = 0;
        
        // ===== CALCULAR OFERTA SI VIENE PRODUCTO PRECARGADO =====
        if ($producto_id) {
            $producto_precargado = Producto::with(['ofertas' => function($query) {
                    $query->where('activa', 1)
                        ->where('fecha_inicio', '<=', now())
                        ->where('fecha_fin', '>=', now());
                }])
                ->where('id', $producto_id)
                ->where('activo', 1)
                ->first();
            
            if ($producto_precargado) {
                $precio_con_oferta = $producto_precargado->precio;
                
                if ($producto_precargado->ofertas->isNotEmpty()) {
                    $oferta = $producto_precargado->ofertas->first();
                    
                    if ($oferta->tipo == 'porcentaje') {
                        $precio_con_oferta = $producto_precargado->precio * (1 - $oferta->valor / 100);
                    } else {
                        $precio_con_oferta = $producto_precargado->precio - $oferta->valor;
                    }
                }
                
                // También obtener existencias en esta sucursal
                $productoEnSucursal = $sucursal->productos()
                    ->where('producto_id', $producto_id)
                    ->first();
                    
                $producto_precargado->existencias = $productoEnSucursal->pivot->existencias ?? 0;
            }
        }
        
        // Datos del cliente (por defecto vacíos)
        $cliente_datos = [
            'nombre' => '',
            'telefono' => '',
            'email' => '',
            'direccion' => '',
            'ciudad' => '',
            'estado' => '',
            'codigo_postal' => '',
            'notas' => ''
        ];
        
        // Si viene un teléfono en la URL, buscar al cliente
        if ($request->has('telefono') && !empty($request->telefono)) {
            $cliente = Cliente::where('telefono', $request->telefono)->first();
            
            if ($cliente) {
                $cliente_datos = [
                    'nombre' => $cliente->nombre,
                    'telefono' => $cliente->telefono,
                    'email' => $cliente->email,
                    'direccion' => $cliente->direccion,
                    'ciudad' => $cliente->ciudad,
                    'estado' => $cliente->estado,
                    'codigo_postal' => $cliente->codigo_postal,
                    'notas' => ''
                ];
            }
        }
        // Si viene un cliente_id en la URL, buscar por ID
        elseif ($request->has('cliente_id') && !empty($request->cliente_id)) {
            $cliente = Cliente::find($request->cliente_id);
            
            if ($cliente) {
                $cliente_datos = [
                    'nombre' => $cliente->nombre,
                    'telefono' => $cliente->telefono,
                    'email' => $cliente->email,
                    'direccion' => $cliente->direccion,
                    'ciudad' => $cliente->ciudad,
                    'estado' => $cliente->estado,
                    'codigo_postal' => $cliente->codigo_postal,
                    'notas' => ''
                ];
            }
        }
        
        return view('vendedor.pedidos.create', compact(
            'productos', 
            'sucursal', 
            'cliente_datos',
            'producto_id',
            'cantidad',
            'producto_precargado',
            'precio_con_oferta'
        ));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }
        
        $request->validate([
            'cliente_nombre' => 'required|string|max:100',
            'cliente_telefono' => 'required|string|max:20',
            'cliente_email' => 'nullable|email|max:100',
            'cliente_direccion' => 'required|string',
            'cliente_ciudad' => 'required|string|max:100',
            'cliente_estado' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:5',
            'notas' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*' => 'required|exists:productos,id',
            'cantidades' => 'required|array|min:1',
            'cantidades.*' => 'required|integer|min:1',
            'distancia_km' => 'required|numeric|min:0'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Buscar cliente por teléfono o correo
            $cliente = Cliente::where('telefono', $request->cliente_telefono)
                        ->orWhere('email', $request->cliente_email)
                        ->first();
            
            // Si NO existe, CREAR EL CLIENTE AUTOMÁTICAMENTE
            if (!$cliente && !empty($request->cliente_email)) {
                $password = $this->generateRandomPassword();
                
                $cliente = Cliente::create([
                    'nombre' => $request->cliente_nombre,
                    'email' => $request->cliente_email,
                    'password' => Hash::make($password),
                    'telefono' => $request->cliente_telefono,
                    'direccion' => $request->cliente_direccion,
                    'ciudad' => $request->cliente_ciudad,
                    'estado' => $request->cliente_estado,
                    'codigo_postal' => $request->codigo_postal,
                    'activo' => true,
                    'email_verified_at' => now()
                ]);
                
                \Log::info('Cliente creado automáticamente desde pedido: ' . $cliente->id . ' - Email: ' . $cliente->email);
            }
            
            $fecha = date('ymd');
            $numero = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $folio = 'PED-' . $fecha . '-' . $numero;
            
            $total = 0;
            $productos_data = [];
            
            foreach ($request->productos as $index => $producto_id) {
                if (isset($request->cantidades[$index]) && $request->cantidades[$index] > 0) {
                    // ===== IMPORTANTE: Cargar producto con OFERTAS =====
                    $producto = Producto::with(['ofertas' => function($query) {
                            $query->where('activa', 1)
                                ->where('fecha_inicio', '<=', now())
                                ->where('fecha_fin', '>=', now());
                        }])
                        ->findOrFail($producto_id);
                    
                    $cantidad = $request->cantidades[$index];
                    
                    // Verificar existencias
                    $existencias = DB::table('producto_sucursal')
                        ->where('producto_id', $producto_id)
                        ->where('sucursal_id', $sucursal->id)
                        ->value('existencias') ?? 0;
                    
                    if ($existencias < $cantidad) {
                        throw new \Exception("No hay suficientes existencias de {$producto->nombre}. Disponibles: {$existencias}");
                    }
                    
                    // ===== CALCULAR PRECIO CON OFERTA =====
                    $precioUnitario = $producto->precio;
                    
                    if ($producto->ofertas->isNotEmpty()) {
                        $oferta = $producto->ofertas->first();
                        
                        if ($oferta->tipo == 'porcentaje') {
                            $precioUnitario = $producto->precio * (1 - $oferta->valor / 100);
                        } else {
                            $precioUnitario = $producto->precio - $oferta->valor;
                        }
                    }
                    
                    $subtotal = $precioUnitario * $cantidad;
                    $total += $subtotal;
                    
                    $productos_data[] = [
                        'producto_id' => $producto_id,
                        'producto_nombre' => $producto->nombre,
                        'cantidad' => $cantidad,
                        'precio' => $precioUnitario, // PRECIO CON OFERTA
                        'precio_original' => $producto->precio
                    ];
                }
            }
            
            // Crear el pedido
            $pedido = Pedido::create([
                'cliente_id' => $cliente ? $cliente->id : null,
                'folio' => $folio,
                'cliente_nombre' => $request->cliente_nombre,
                'cliente_telefono' => $request->cliente_telefono,
                'cliente_direccion' => $request->cliente_direccion,
                'cliente_ciudad' => $request->cliente_ciudad,
                'cliente_estado' => $request->cliente_estado,
                'codigo_postal' => $request->codigo_postal,
                'total' => $total,
                'metodo_pago' => 'manual',
                'pago_confirmado' => 1,
                'estado' => 'pendiente',
                'fecha' => now(),
                'notas' => $request->notas,
                'sucursal_id' => $sucursal->id,
                'distancia_km' => $request->distancia_km,
                'cobertura_verificada' => 1
            ]);
            
            // Guardar los items del pedido
            foreach ($productos_data as $item) {
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'producto_nombre' => $item['producto_nombre'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'] // PRECIO CON OFERTA
                ]);
                
                // Reducir existencias
                DB::table('producto_sucursal')
                    ->where('producto_id', $item['producto_id'])
                    ->where('sucursal_id', $sucursal->id)
                    ->decrement('existencias', $item['cantidad']);
            }
            
            // Asignar responsable
            PedidoResponsable::create([
                'pedido_id' => $pedido->id,
                'usuario_id' => $usuario_id,
                'fecha_asignacion' => now()
            ]);
            
            // Registrar historial
            PedidoHistorial::create([
                'pedido_id' => $pedido->id,
                'usuario_id' => $usuario_id,
                'accion' => 'creado',
                'detalles' => "Pedido creado por vendedor {$usuario->nombre}",
                'fecha' => now()
            ]);
            
            DB::commit();
            
            session()->forget('cobertura_verificada_vendedor');
            
            // Mensaje personalizado
            if ($cliente && $cliente->wasRecentlyCreated) {
                $mensaje = "¡Pedido #{$folio} creado exitosamente! Se ha registrado al cliente con acceso al sistema. Email: {$cliente->email}";
            } else {
                $mensaje = "¡Pedido #{$folio} creado exitosamente!";
            }
            
            return redirect()->route('vendedor.pedidos.hoy')
                ->with('success', $mensaje);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al crear pedido: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Generate a random password for new clients
     */
    private function generateRandomPassword($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

    public function show($id)
    {
        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }

        $pedido = Pedido::with('sucursal')->find($id);

        if (!$pedido) {
            return redirect()->route('vendedor.pedidos.index')
                ->with('error', 'El pedido no existe.');
        }

        if ($pedido->sucursal_id != $sucursal->id) {
            return redirect()->route('vendedor.pedidos.index')
                ->with('error', 'No tienes acceso a pedidos de otras sucursales.');
        }

        $pedido->items = PedidoItem::where('pedido_id', $pedido->id)->get();
        
        $responsables = PedidoResponsable::where('pedido_id', $pedido->id)
            ->with('usuario')
            ->get();
        $pedido->responsables = $responsables;
        
        $historial = PedidoHistorial::where('pedido_id', $pedido->id)
            ->with('usuario')
            ->orderBy('fecha', 'desc')
            ->get();
        $pedido->historial = $historial;

        $esResponsable = $responsables->contains('usuario_id', $usuario_id);

        // FUNCIONALIDAD CHIDA: Si ve el pedido y no tiene responsable, se asigna automáticamente
        if (!$esResponsable && $responsables->isEmpty()) {
            try {
                DB::beginTransaction();
                
                PedidoResponsable::create([
                    'pedido_id' => $pedido->id,
                    'usuario_id' => $usuario_id,
                    'fecha_asignacion' => now()
                ]);
                
                PedidoHistorial::create([
                    'pedido_id' => $pedido->id,
                    'usuario_id' => $usuario_id,
                    'accion' => 'responsable_asignado',
                    'detalles' => "Vendedor {$usuario->nombre} asignado al pedido al ver los detalles",
                    'fecha' => now()
                ]);
                
                DB::commit();
                
                // Recargar responsables
                $pedido->responsables = PedidoResponsable::where('pedido_id', $pedido->id)
                    ->with('usuario')
                    ->get();
                
                $esResponsable = true;
                
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }

        return view('vendedor.pedidos.show', compact('pedido', 'esResponsable', 'sucursal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,enviado,entregado,cancelado',
            'pago_confirmado' => 'sometimes|boolean',
            'fecha_entrega' => 'nullable|date',
            'comentario' => 'nullable|string|max:500'
        ]);

        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }

        try {
            DB::beginTransaction();

            $pedido = Pedido::where('id', $id)
                ->where('sucursal_id', $sucursal->id)
                ->first();

            if (!$pedido) {
                return redirect()->route('vendedor.pedidos.index')
                    ->with('error', 'Pedido no encontrado.');
            }

            $estado_actual = $pedido->estado;
            $pago_actual = $pedido->pago_confirmado;
            $fecha_entrega_actual = $pedido->fecha_entrega;

            $nuevo_estado = $request->estado;
            $nuevo_pago = $request->has('pago_confirmado') ? 1 : 0;
            $nueva_fecha = $request->fecha_entrega;

            // ===== REGLA DE NEGOCIO: Manejo de stock al cancelar/descancelar =====
            $items = PedidoItem::where('pedido_id', $pedido->id)->get();
            
            // Si se CANCELA (de activo a cancelado)
            if ($nuevo_estado == 'cancelado' && $estado_actual != 'cancelado') {
                foreach ($items as $item) {
                    if ($item->producto_id) {
                        // DEVOLVER stock
                        DB::table('producto_sucursal')
                            ->where('producto_id', $item->producto_id)
                            ->where('sucursal_id', $sucursal->id)
                            ->increment('existencias', $item->cantidad);
                    }
                }
            }
            
            // Si se DES-CANCELA (de cancelado a activo)
            if ($estado_actual == 'cancelado' && $nuevo_estado != 'cancelado') {
                foreach ($items as $item) {
                    if ($item->producto_id) {
                        // VOLVER A RESTAR stock (como cuando se creó)
                        DB::table('producto_sucursal')
                            ->where('producto_id', $item->producto_id)
                            ->where('sucursal_id', $sucursal->id)
                            ->decrement('existencias', $item->cantidad);
                    }
                }
            }

            $pedido->estado = $nuevo_estado;
            $pedido->pago_confirmado = $nuevo_pago;
            $pedido->fecha_entrega = $nueva_fecha;
            $pedido->save();

            $comentario = $request->comentario ?? '';

            if ($nuevo_estado != $estado_actual) {
                PedidoHistorial::create([
                    'pedido_id' => $pedido->id,
                    'usuario_id' => $usuario_id,
                    'accion' => 'cambio_estado',
                    'detalles' => "Estado cambiado de '{$estado_actual}' a '{$nuevo_estado}'. {$comentario}",
                    'fecha' => now()
                ]);
            }

            if ($nuevo_pago != $pago_actual) {
                PedidoHistorial::create([
                    'pedido_id' => $pedido->id,
                    'usuario_id' => $usuario_id,
                    'accion' => 'cambio_pago',
                    'detalles' => "Pago " . ($nuevo_pago ? "confirmado" : "pendiente") . ". {$comentario}",
                    'fecha' => now()
                ]);
            }

            if ($nueva_fecha != $fecha_entrega_actual) {
                PedidoHistorial::create([
                    'pedido_id' => $pedido->id,
                    'usuario_id' => $usuario_id,
                    'accion' => 'cambio_fecha',
                    'detalles' => "Fecha de entrega: " . ($nueva_fecha ?? "sin fecha") . ". {$comentario}",
                    'fecha' => now()
                ]);
            }

            $responsable = PedidoResponsable::where('pedido_id', $pedido->id)
                ->where('usuario_id', $usuario_id)
                ->first();
                
            if (!$responsable) {
                PedidoResponsable::create([
                    'pedido_id' => $pedido->id,
                    'usuario_id' => $usuario_id,
                    'fecha_asignacion' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('vendedor.pedidos.show', $pedido->id)
                ->with('success', '¡Pedido actualizado correctamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('vendedor.pedidos.show', $id)
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function hoy(Request $request)
    {
        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }

        $sucursal_id = $sucursal->id;
        $sucursalNombre = $sucursal->nombre;

        $fechaHoy = Carbon::today();

        // ===== REGLAS DE NEGOCIO: Mis pedidos + disponibles =====
        $pedidos_hoy = Pedido::where('sucursal_id', $sucursal_id)
            ->whereDate('fecha', $fechaHoy)
            ->where(function($query) use ($usuario_id) {
                $query->whereDoesntHave('responsables') // Sin asignar
                      ->orWhereHas('responsables', function($q) use ($usuario_id) {
                          $q->where('usuario_id', $usuario_id); // Mis pedidos
                      });
            })
            ->withCount('items as items_count')
            ->orderBy('fecha', 'desc')
            ->get();

        // Cargar responsables para cada pedido
        foreach ($pedidos_hoy as $pedido) {
            $pedido->responsables = PedidoResponsable::where('pedido_id', $pedido->id)
                ->with('usuario')
                ->get();
        }

        // Separar para estadísticas
        $misPedidos = $pedidos_hoy->filter(function($pedido) use ($usuario_id) {
            return $pedido->responsables->contains('usuario_id', $usuario_id);
        });
        
        $pedidosDisponibles = $pedidos_hoy->filter(function($pedido) {
            return $pedido->responsables->isEmpty();
        });

        $contadores = [
            'total' => $pedidos_hoy->count(),
            'mis_pedidos' => $misPedidos->count(),
            'disponibles' => $pedidosDisponibles->count(),
            'pendiente' => $pedidos_hoy->where('estado', 'pendiente')->count(),
            'confirmado' => $pedidos_hoy->where('estado', 'confirmado')->count(),
            'enviado' => $pedidos_hoy->where('estado', 'enviado')->count(),
            'entregado' => $pedidos_hoy->where('estado', 'entregado')->count(),
            'cancelado' => $pedidos_hoy->where('estado', 'cancelado')->count()
        ];

        $ventas_hoy = $pedidos_hoy->where('estado', 'entregado')
            ->where('pago_confirmado', 1)
            ->sum('total') ?? 0;

        $pedidos_urgentes = $pedidos_hoy->where('estado', 'pendiente')
            ->filter(function($pedido) {
                return Carbon::parse($pedido->fecha)->diffInHours(now()) >= 1;
            })
            ->count();

        $pedidos_pendientes = $contadores['pendiente'];

        return view('vendedor.pedidos.hoy', compact(
            'pedidos_hoy',
            'misPedidos',
            'pedidosDisponibles',
            'contadores',
            'ventas_hoy',
            'pedidos_urgentes',
            'pedidos_pendientes',
            'sucursal',
            'sucursalNombre'
        ));
    }

    public function seguimiento($id)
    {
        $usuario = Auth::user();
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }

        $pedido = Pedido::where('id', $id)
            ->where('sucursal_id', $sucursal->id)
            ->first();

        if (!$pedido) {
            return redirect()->route('vendedor.pedidos.index')
                ->with('error', 'Pedido no encontrado.');
        }

        $pedido->items = PedidoItem::where('pedido_id', $pedido->id)->get();
        $historial = PedidoHistorial::where('pedido_id', $pedido->id)
            ->with('usuario')
            ->orderBy('fecha', 'desc')
            ->get();

        $info = [
            'sucursal_nombre' => $sucursal->nombre,
            'pedido_folio' => $pedido->folio,
            'estado' => $pedido->estado
        ];

        return view('vendedor.pedidos.seguimiento', compact('pedido', 'historial', 'info'));
    }

    public function guardarSeguimiento(Request $request, $id)
    {
        $request->validate([
            'tipo' => 'required|in:observacion,seguimiento,contacto',
            'comentario' => 'required|string|max:500'
        ]);

        $usuario = Auth::user();
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }

        $pedido = Pedido::where('id', $id)
            ->where('sucursal_id', $sucursal->id)
            ->first();

        if (!$pedido) {
            return redirect()->route('vendedor.pedidos.index')
                ->with('error', 'Pedido no encontrado.');
        }

        try {
            PedidoHistorial::create([
                'pedido_id' => $pedido->id,
                'usuario_id' => $usuario->id,
                'accion' => $request->tipo,
                'detalles' => $request->comentario,
                'fecha' => now()
            ]);

            return redirect()->route('vendedor.pedidos.seguimiento', $pedido->id)
                ->with('success', 'Seguimiento agregado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al agregar seguimiento: ' . $e->getMessage());
        }
    }

    public function asignar(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|integer|exists:pedidos,id'
        ]);

        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes una sucursal asignada.'
            ], 403);
        }
        
        $sucursal_id = $sucursal->id;
        $pedido_id = $request->pedido_id;

        try {
            DB::beginTransaction();

            $pedido = Pedido::where('id', $pedido_id)
                ->where('sucursal_id', $sucursal_id)
                ->whereIn('estado', ['pendiente', 'confirmado'])
                ->first();

            if (!$pedido) {
                return response()->json([
                    'success' => false,
                    'message' => 'El pedido no está disponible en tu sucursal o ya fue procesado'
                ], 404);
            }

            $vendedoresAsignados = PedidoResponsable::where('pedido_id', $pedido_id)
                ->with('usuario')
                ->get();
            
            if ($vendedoresAsignados->count() > 0) {
                $nombres = $vendedoresAsignados->pluck('usuario.nombre')->implode(', ');
                    
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido ya está asignado a: ' . $nombres
                ], 400);
            }

            PedidoResponsable::create([
                'pedido_id' => $pedido_id,
                'usuario_id' => $usuario_id,
                'fecha_asignacion' => now()
            ]);

            PedidoHistorial::create([
                'pedido_id' => $pedido_id,
                'usuario_id' => $usuario_id,
                'accion' => 'responsable_asignado',
                'detalles' => "Vendedor {$usuario->nombre} asignado al pedido",
                'fecha' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Pedido asignado correctamente! Ahora eres el responsable.',
                'folio' => $pedido->folio
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPedidos($usuario_id, $sucursal_id, $disponibles, $estado, $desde, $hasta)
    {
        $query = Pedido::query();

        if ($disponibles) {
            $query->where('sucursal_id', $sucursal_id)
                ->whereIn('estado', ['pendiente', 'confirmado'])
                ->whereDoesntHave('responsables');
        } else {
            $query->whereHas('responsables', function($q) use ($usuario_id) {
                    $q->where('usuario_id', $usuario_id);
                })
                ->where('sucursal_id', $sucursal_id);
        }

        if ($estado != 'todos') {
            $query->where('estado', $estado);
        }

        if ($desde) {
            $query->whereDate('fecha', '>=', Carbon::parse($desde));
        }
        if ($hasta) {
            $query->whereDate('fecha', '<=', Carbon::parse($hasta));
        }

        return $query->withCount('items as items_count')
            ->orderBy('fecha', 'desc')
            ->get();
    }

    private function getContadores($usuario_id, $sucursal_id)
    {
        $contadores = [
            'todos' => 0,
            'pendiente' => 0,
            'confirmado' => 0,
            'enviado' => 0,
            'entregado' => 0,
            'cancelado' => 0
        ];

        foreach (array_keys($contadores) as $estado) {
            $query = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                    $q->where('usuario_id', $usuario_id);
                })
                ->where('sucursal_id', $sucursal_id);

            if ($estado != 'todos') {
                $query->where('estado', $estado);
            }

            $contadores[$estado] = $query->count();
        }

        return $contadores;
    }

    private function getTotalDisponibles($sucursal_id)
    {
        return Pedido::where('sucursal_id', $sucursal_id)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->whereDoesntHave('responsables')
            ->count();
    }

    public function verificarOferta(Request $request)
    {
        $producto = Producto::with(['ofertas' => function($query) {
                $query->where('activa', 1)
                    ->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now());
            }])
            ->find($request->producto_id);
        
        if ($producto && $producto->ofertas->isNotEmpty()) {
            $oferta = $producto->ofertas->first();
            $precioFinal = $producto->precio;
            
            if ($oferta->tipo == 'porcentaje') {
                $precioFinal = $producto->precio * (1 - $oferta->valor / 100);
            } else {
                $precioFinal = $producto->precio - $oferta->valor;
            }
            
            return response()->json([
                'en_oferta' => true,
                'precio_final' => $precioFinal,
                'porcentaje' => $oferta->valor
            ]);
        }
        
        return response()->json(['en_oferta' => false]);
    }
}