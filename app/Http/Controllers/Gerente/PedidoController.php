<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PedidoHistorial;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Sucursal;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PedidoController extends Controller
{
    protected $sucursal;
    protected $sucursalId;
    protected $sucursalNombre;
    
    // CONSTANTES PARA CONTROL DE STOCK
    const ESTADOS_CON_STOCK = ['pendiente', 'confirmado', 'enviado', 'entregado'];
    const ESTADO_SIN_STOCK = 'cancelado';

    public function __construct()
    {
        // Verificar que el usuario es gerente
        $user = auth()->user();
        
        if (!$user || $user->rol !== 'gerente') {
            abort(403, 'Acceso no autorizado - Se requieren permisos de gerente');
        }
        
        // Obtener la sucursal del gerente desde usuario_sucursal
        $usuarioSucursal = DB::table('usuario_sucursal')
            ->where('usuario_id', $user->id)
            ->first();
        
        if (!$usuarioSucursal) {
            abort(403, 'No tienes una sucursal asignada. Contacta al administrador.');
        }
        
        $this->sucursalId = $usuarioSucursal->sucursal_id;
        
        // Obtener datos de la sucursal
        $this->sucursal = Sucursal::find($this->sucursalId);
        $this->sucursalNombre = $this->sucursal ? $this->sucursal->nombre : 'Sucursal no encontrada';
        
        // Guardar en sesión para el sidebar
        session([
            'sucursal_nombre' => $this->sucursalNombre,
            'sucursal_id' => $this->sucursalId
        ]);
    }

    /**
     * ✅ MÉTODO 1: Verificar si el pedido ya tiene stock descontado
     */
    private function tieneStockDescontado($pedido)
    {
        return PedidoHistorial::where('pedido_id', $pedido->id)
            ->where('accion', 'stock_descontado')
            ->exists();
    }

    /**
     * ✅ MÉTODO 2: Verificar si el pedido ya tiene stock regresado
     */
    private function tieneStockRegresado($pedido)
    {
        return PedidoHistorial::where('pedido_id', $pedido->id)
            ->where('accion', 'stock_regresado')
            ->exists();
    }

    /**
     * ✅ MÉTODO 3: Validar stock suficiente antes de descontar
     */
    private function validarStockSuficiente($pedido)
    {
        foreach ($pedido->items as $item) {
            $existencias = DB::table('producto_sucursal')
                ->where('producto_id', $item->producto_id)
                ->where('sucursal_id', $pedido->sucursal_id)
                ->value('existencias') ?? 0;
            
            if ($existencias < $item->cantidad) {
                throw new \Exception("Stock insuficiente para {$item->producto_nombre}. Disponible: {$existencias}, Requerido: {$item->cantidad}");
            }
        }
        return true;
    }

    /**
     * ✅ MÉTODO 4: Descontar stock del inventario (con verificación)
     */
    private function descontarStockSeguro($pedido)
    {
        // Si ya está descontado, no hacer nada
        if ($this->tieneStockDescontado($pedido)) {
            Log::info("Stock ya descontado para pedido {$pedido->id}");
            return false;
        }
        
        // Validar stock suficiente
        $this->validarStockSuficiente($pedido);
        
        foreach ($pedido->items as $item) {
            DB::table('producto_sucursal')
                ->where('producto_id', $item->producto_id)
                ->where('sucursal_id', $pedido->sucursal_id)
                ->decrement('existencias', $item->cantidad);
        }
        
        // Registrar en historial que se descontó stock
        PedidoHistorial::create([
            'pedido_id' => $pedido->id,
            'usuario_id' => auth()->id(),
            'accion' => 'stock_descontado',
            'detalles' => 'Stock descontado del inventario',
            'fecha' => now()
        ]);
        
        Log::info("Stock descontado para pedido {$pedido->id}");
        return true;
    }

    /**
     * ✅ MÉTODO 5: Regresar stock al inventario (con verificación)
     */
    private function regresarStockSeguro($pedido)
    {
        // Si ya está regresado, no hacer nada
        if ($this->tieneStockRegresado($pedido)) {
            Log::info("Stock ya regresado para pedido {$pedido->id}");
            return false;
        }
        
        foreach ($pedido->items as $item) {
            DB::table('producto_sucursal')
                ->where('producto_id', $item->producto_id)
                ->where('sucursal_id', $pedido->sucursal_id)
                ->increment('existencias', $item->cantidad);
        }
        
        // Registrar en historial que se regresó stock
        PedidoHistorial::create([
            'pedido_id' => $pedido->id,
            'usuario_id' => auth()->id(),
            'accion' => 'stock_regresado',
            'detalles' => 'Stock regresado al inventario',
            'fecha' => now()
        ]);
        
        Log::info("Stock regresado para pedido {$pedido->id}");
        return true;
    }

    /**
     * ✅ MÉTODO 6: Sincronizar stock según cambio de estado
     */
    private function sincronizarStockPorEstado($pedido, $nuevoEstado)
    {
        $estadoAnterior = $pedido->getOriginal('estado');
        
        // Si el estado no cambió, no hacer nada
        if ($estadoAnterior == $nuevoEstado) {
            return;
        }
        
        $estadosConStock = self::ESTADOS_CON_STOCK;
        $estadoSinStock = self::ESTADO_SIN_STOCK;
        
        // Caso 1: De estado con stock a cancelado (REGRESAR STOCK)
        if (in_array($estadoAnterior, $estadosConStock) && $nuevoEstado == $estadoSinStock) {
            $this->regresarStockSeguro($pedido);
        }
        
        // Caso 2: De cancelado a estado con stock (DESCONTAR STOCK)
        if ($estadoAnterior == $estadoSinStock && in_array($nuevoEstado, $estadosConStock)) {
            $this->descontarStockSeguro($pedido);
        }
        
        // Caso 3: De un estado con stock a otro estado con stock (NO CAMBIA STOCK)
        // Ejemplo: de pendiente a confirmado - no se modifica stock
    }

    /**
     * Lista todos los pedidos de la sucursal del gerente
     */
    public function index(Request $request)
    {
        // Contadores para el sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')
            ->where('sucursal_id', $this->sucursalId)
            ->count();
            
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursalId)
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        // Obtener vendedores de la sucursal para mostrar en la vista
        $vendedores = Usuario::where('rol', 'vendedor')
            ->where('activo', true)
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->orderBy('nombre')
            ->get();

        // Filtros
        $estado = $request->get('estado');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');
        $busqueda = $request->get('busqueda');

        // Construir consulta - SOLO pedidos de la sucursal del gerente
        $query = Pedido::withCount('items')
            ->with('sucursal')
            ->where('sucursal_id', $this->sucursalId);

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($fecha_inicio) {
            $query->whereDate('fecha', '>=', $fecha_inicio);
        }

        if ($fecha_fin) {
            $query->whereDate('fecha', '<=', $fecha_fin);
        }

        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('folio', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cliente_nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cliente_telefono', 'LIKE', "%{$busqueda}%");
            });
        }

        $pedidos = $query->orderBy('fecha', 'desc')->get()
            ->map(function($pedido) {
                $pedido->fecha = Carbon::parse($pedido->fecha);
                return $pedido;
            });

        // Contar por estado para estadísticas
        $estados_count = Pedido::select('estado', DB::raw('count(*) as total'))
            ->where('sucursal_id', $this->sucursalId)
            ->groupBy('estado')
            ->get()
            ->map(function($item) {
                $item->total = (int)$item->total;
                return $item;
            });

        // Calcular total general
        $total_general = $pedidos->sum('total');
        $sucursal = $this->sucursal;

        return view('gerente.pedidos.index', compact(
            'pedidos',
            'estados_count',
            'total_general',
            'estado',
            'fecha_inicio',
            'fecha_fin',
            'busqueda',
            'vendedores',
            'sucursal'
        ));
    }

    /**
     * Muestra el formulario para crear un nuevo pedido
     */
    public function create(Request $request)
    {
        // Actualizar contadores para el sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')
            ->where('sucursal_id', $this->sucursalId)
            ->count();
            
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursalId)
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        // Obtener productos activos con sus existencias en la sucursal
        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->map(function($producto) {
                $existencias = DB::table('producto_sucursal')
                    ->where('producto_id', $producto->id)
                    ->where('sucursal_id', $this->sucursalId)
                    ->value('existencias') ?? 0;
                
                $producto->existencias = $existencias;
                return $producto;
            });

        // ✅ OBTENER VENDEDORES DE LA SUCURSAL (NUEVO)
        $vendedores = Usuario::where('rol', 'vendedor')
            ->where('activo', true)
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'usuario']);

        // Datos del cliente desde sesión o request
        $cliente_datos = [
            'nombre' => $request->get('cliente', ''),
            'telefono' => $request->get('telefono', ''),
            'ciudad' => $request->get('ciudad', ''),
            'estado' => $request->get('estado', ''),
            'direccion' => $request->get('direccion', ''),
            'codigo_postal' => $request->get('codigo_postal', ''),
            'notas' => $request->get('notas', '')
        ];

        // Variables para producto precargado
        $producto_id = $request->get('producto_id');
        $cantidad = $request->get('cantidad', 1);
        $producto_precargado = null;
        $precio_con_oferta = 0;

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
                
                // Obtener existencias en la sucursal
                $existencias = DB::table('producto_sucursal')
                    ->where('producto_id', $producto_id)
                    ->where('sucursal_id', $this->sucursalId)
                    ->value('existencias') ?? 0;
                
                $producto_precargado->existencias = $existencias;
            }
        }

        return view('gerente.pedidos.create', compact(
            'productos',
            'cliente_datos',
            'vendedores', // ✅ NUEVO
            'producto_precargado',
            'precio_con_oferta',
            'cantidad'
        ));
    }

    /**
     * Guarda un nuevo pedido en la base de datos
     */
    public function store(Request $request)
    {
        Log::info('=== INICIO CREACIÓN DE PEDIDO (GERENTE) ===');
        
        $validated = $request->validate([
            'cliente_nombre' => 'required|string|max:100',
            'cliente_telefono' => 'required|string|max:20',
            'cliente_direccion' => 'required|string|max:255',
            'cliente_ciudad' => 'required|string|max:100',
            'cliente_estado' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:5',
            'notas' => 'nullable|string|max:500',
            'productos' => 'required|array|min:1',
            'productos.*' => 'required|integer|exists:productos,id',
            'cantidades' => 'required|array',
            'cantidades.*' => 'required|integer|min:1',
            'distancia_km' => 'nullable|numeric'
        ]);

        // Verificar que la sucursal en sesión existe
        if (!$this->sucursalId) {
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error de sucursal',
                    'message' => 'No se pudo identificar tu sucursal. Contacta al administrador.'
                ]);
        }

        $productos_data = [];
        $total = 0;
        $error_existencias = false;
        $productosError = [];

        // Procesar productos y verificar existencias
        foreach ($request->productos as $index => $producto_id) {
            if (!empty($producto_id) && isset($request->cantidades[$index])) {
                $cantidad = (int)$request->cantidades[$index];
                
                if ($cantidad > 0) {
                    $producto = Producto::find($producto_id);
                    
                    if ($producto) {
                        // Obtener existencias en la sucursal del gerente
                        $existencias = DB::table('producto_sucursal')
                            ->where('producto_id', $producto_id)
                            ->where('sucursal_id', $this->sucursalId)
                            ->value('existencias') ?? 0;
                        
                        if ($existencias < $cantidad) {
                            $error_existencias = true;
                            $productosError[] = "{$producto->nombre} (Disponibles: {$existencias})";
                        }
                        
                        $subtotal = $producto->precio * $cantidad;
                        $total += $subtotal;
                        
                        $productos_data[] = [
                            'producto_id' => $producto_id,
                            'producto_nombre' => $producto->nombre,
                            'cantidad' => $cantidad,
                            'precio' => $producto->precio,
                            'subtotal' => $subtotal
                        ];
                    }
                }
            }
        }

        // Si hay error de existencias
        if ($error_existencias) {
            $mensaje = 'No hay suficientes existencias para: ' . implode(', ', $productosError);
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error de inventario',
                    'message' => $mensaje
                ]);
        }

        // Validar que haya al menos un producto
        if (empty($productos_data)) {
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Debe agregar al menos un producto válido'
                ]);
        }

        // Generar folio único
        $folio = 'PED-' . date('Ymd') . '-' . rand(1000, 9999);

        try {
            DB::beginTransaction();

            // Crear el pedido
            $pedido = Pedido::create([
                'folio' => $folio,
                'cliente_nombre' => $request->cliente_nombre,
                'cliente_telefono' => $request->cliente_telefono,
                'cliente_direccion' => $request->cliente_direccion,
                'cliente_ciudad' => $request->cliente_ciudad,
                'cliente_estado' => $request->cliente_estado,
                'codigo_postal' => $request->codigo_postal,
                'sucursal_id' => $this->sucursalId,
                'sucursal_asignada' => $this->sucursalNombre,
                'distancia_km' => $request->distancia_km ?? 0,
                'cobertura_verificada' => true,
                'total' => $total,
                'notas' => $request->notas,
                'estado' => 'pendiente',
                'metodo_pago' => 'manual',
                'pago_confirmado' => false
            ]);

            Log::info('Pedido creado con ID: ' . $pedido->id);

            // Insertar items
            foreach ($productos_data as $producto) {
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto['producto_id'],
                    'producto_nombre' => $producto['producto_nombre'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio']
                ]);
            }

            // ✅ DESCONTAR STOCK SEGURO (con verificación)
            $this->descontarStockSeguro($pedido);

            // Registrar en historial
            $usuario_id = auth()->id();
            PedidoHistorial::create([
                'pedido_id' => $pedido->id,
                'usuario_id' => $usuario_id,
                'accion' => 'creado',
                'detalles' => 'Pedido creado por gerente en sucursal ' . $this->sucursalNombre,
                'fecha' => now()
            ]);

            DB::commit();

            // Limpiar cobertura de sesión
            session()->forget('cobertura_verificada');

            return redirect()->route('gerente.pedidos')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Pedido creado!',
                    'message' => "Pedido {$folio} creado correctamente en tu sucursal"
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear pedido (gerente): ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al crear el pedido: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Muestra los detalles de un pedido específico
     */
    public function show($id)
    {
        // Obtener pedido - SOLO si pertenece a la sucursal del gerente
        $pedido = Pedido::with(['items', 'sucursal'])
            ->where('sucursal_id', $this->sucursalId)
            ->where('id', $id)
            ->first();

        if (!$pedido) {
            return redirect()->route('gerente.pedidos')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error de permiso',
                    'message' => 'No tienes permiso para ver este pedido o no existe en tu sucursal.'
                ]);
        }

        $pedido->fecha = Carbon::parse($pedido->fecha);
        
        $items = $pedido->items;
        foreach ($items as $item) {
            $producto = Producto::find($item->producto_id);
            if ($producto) {
                $item->codigo = $producto->codigo;
                $item->litros = $producto->litros;
            }
        }
        
        $pedido->total_items = $items->sum('cantidad');

        // Obtener responsable actual
        $responsable = DB::table('pedido_responsables')
            ->join('usuarios', 'pedido_responsables.usuario_id', '=', 'usuarios.id')
            ->where('pedido_responsables.pedido_id', $id)
            ->select('usuarios.id', 'usuarios.nombre', 'usuarios.usuario', 'usuarios.rol')
            ->first();

        // Obtener vendedores de la sucursal
        $vendedores = Usuario::where('rol', 'vendedor')
            ->where('activo', true)
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->orderBy('nombre')
            ->get();

        // Determinar si el gerente puede editar el pedido
        $puede_editar = false;
        if ($responsable) {
            if ($responsable->id == auth()->id()) {
                $puede_editar = true;
            }
        } else {
            $puede_editar = true;
        }

        // Obtener historial del pedido
        $historial = PedidoHistorial::with('usuario')
            ->where('pedido_id', $id)
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function($item) {
                $item->usuario_nombre = $item->usuario->nombre ?? 'Sistema';
                $item->usuario_rol = $item->usuario->rol ?? 'sistema';
                return $item;
            });

        return view('gerente.pedidos.show', compact(
            'pedido',
            'items',
            'responsable',
            'vendedores',
            'historial',
            'puede_editar'
        ));
    }

    /**
     * Muestra el formulario para editar un pedido
     */
    public function edit($id)
    {
        // Obtener pedido - SOLO si pertenece a la sucursal del gerente
        $pedido = Pedido::with('sucursal')
            ->where('sucursal_id', $this->sucursalId)
            ->where('id', $id)
            ->first();

        if (!$pedido) {
            return redirect()->route('gerente.pedidos')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error de permiso',
                    'message' => 'No tienes permiso para editar este pedido o no existe en tu sucursal.'
                ]);
        }

        $pedido->fecha = Carbon::parse($pedido->fecha);

        // Obtener vendedores de la sucursal
        $vendedores = Usuario::where('rol', 'vendedor')
            ->where('activo', true)
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->orderBy('nombre')
            ->get();

        // Obtener el vendedor responsable actual
        $responsable_actual = DB::table('pedido_responsables')
            ->join('usuarios', 'pedido_responsables.usuario_id', '=', 'usuarios.id')
            ->where('pedido_responsables.pedido_id', $id)
            ->select('usuarios.id', 'usuarios.nombre', 'usuarios.usuario')
            ->first();
        
        $vendedor_actual_id = $responsable_actual ? $responsable_actual->id : null;

        // Obtener historial del pedido
        $historial = PedidoHistorial::with('usuario')
            ->where('pedido_id', $id)
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function($item) {
                $item->usuario_nombre = $item->usuario->nombre ?? 'Sistema';
                $item->usuario_rol = $item->usuario->rol ?? 'sistema';
                return $item;
            });

        $usuario_id = auth()->id();
        $usuario_nombre = auth()->user()->nombre ?? 'Gerente';

        // Actualizar contadores para el sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')
            ->where('sucursal_id', $this->sucursalId)
            ->count();
            
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursalId)
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        return view('gerente.pedidos.edit', compact(
            'pedido',
            'vendedores',
            'responsable_actual',
            'vendedor_actual_id',
            'historial',
            'usuario_id',
            'usuario_nombre'
        ));
    }

    /**
     * ✅ MÉTODO MEJORADO: Actualiza un pedido existente (con stock seguro)
     */
    public function update(Request $request, $id)
    {
        // Obtener pedido - SOLO si pertenece a la sucursal del gerente
        $pedido = Pedido::with('items')
            ->where('sucursal_id', $this->sucursalId)
            ->where('id', $id)
            ->first();

        if (!$pedido) {
            return redirect()->route('gerente.pedidos')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'No tienes permiso para editar este pedido'
                ]);
        }

        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,enviado,entregado,cancelado',
            'fecha_entrega' => 'nullable|date',
            'distancia_km' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
            'pago_confirmado' => 'nullable|boolean',
            'cobertura_verificada' => 'nullable|boolean',
            'vendedor_responsable' => 'nullable|exists:usuarios,id'
        ]);

        $usuario_id = auth()->id();

        // Determinar fecha de confirmación
        $fecha_confirmacion = $pedido->fecha_confirmacion;
        if ($request->estado == 'confirmado' && !$pedido->fecha_confirmacion) {
            $fecha_confirmacion = now();
        } elseif ($request->estado != 'confirmado' && $pedido->estado == 'confirmado') {
            $fecha_confirmacion = null;
        }

        // Si el estado cambia a entregado, registrar fecha si no existe
        $fecha_entrega = $request->fecha_entrega;
        if ($request->estado == 'entregado' && !$fecha_entrega) {
            $fecha_entrega = now()->toDateString();
        }

        try {
            DB::beginTransaction();

            // ✅ SINCRONIZAR STOCK SEGURO (evita duplicados)
            $this->sincronizarStockPorEstado($pedido, $request->estado);

            // Actualizar pedido
            $pedido->update([
                'estado' => $request->estado,
                'pago_confirmado' => $request->has('pago_confirmado'),
                'fecha_entrega' => $fecha_entrega,
                'notas' => $request->notas,
                'distancia_km' => $request->distancia_km,
                'cobertura_verificada' => $request->has('cobertura_verificada'),
                'fecha_confirmacion' => $fecha_confirmacion
            ]);

            // Registrar en historial
            $detalles = "Estado cambiado a: " . $request->estado . ". " . 
                       ($request->has('pago_confirmado') ? "Pago confirmado. " : "") . 
                       ($request->has('cobertura_verificada') ? "Cobertura verificada. " : "") .
                       ($fecha_entrega ? "Fecha entrega: " . $fecha_entrega . ". " : "");

            PedidoHistorial::create([
                'pedido_id' => $id,
                'usuario_id' => $usuario_id,
                'accion' => 'actualizacion',
                'detalles' => $detalles,
                'fecha' => now()
            ]);

            // Procesar reasignación de vendedor
            if ($request->filled('vendedor_responsable')) {
                $nuevo_vendedor_id = $request->vendedor_responsable;
                
                // Verificar que el vendedor pertenece a la sucursal o es el gerente
                $vendedorValido = false;
                
                if ($nuevo_vendedor_id == $usuario_id) {
                    $vendedorValido = true; // El gerente se asigna a sí mismo
                } else {
                    $vendedorValido = Usuario::where('id', $nuevo_vendedor_id)
                        ->where('rol', 'vendedor')
                        ->where('activo', true)
                        ->whereHas('sucursales', function($q) {
                            $q->where('sucursal_id', $this->sucursalId);
                        })
                        ->exists();
                }

                if ($vendedorValido) {
                    // Obtener responsable anterior
                    $responsable_anterior = DB::table('pedido_responsables')
                        ->where('pedido_id', $id)
                        ->first();

                    // Eliminar asignaciones anteriores
                    DB::table('pedido_responsables')
                        ->where('pedido_id', $id)
                        ->delete();

                    // Asignar nuevo responsable
                    DB::table('pedido_responsables')->insert([
                        'pedido_id' => $id,
                        'usuario_id' => $nuevo_vendedor_id,
                        'fecha_asignacion' => now()
                    ]);

                    // Registrar en historial
                    $detalles_historial = "Reasignado por gerente";
                    
                    if ($responsable_anterior) {
                        $usuario_anterior = Usuario::find($responsable_anterior->usuario_id);
                        if ($usuario_anterior) {
                            $detalles_historial .= " (anterior: " . $usuario_anterior->nombre . ")";
                        }
                    }

                    if ($nuevo_vendedor_id == $usuario_id) {
                        $detalles_historial .= " (Gerente tomó control)";
                    } else {
                        $nuevo_vendedor = Usuario::find($nuevo_vendedor_id);
                        if ($nuevo_vendedor) {
                            $detalles_historial .= " (nuevo: " . $nuevo_vendedor->nombre . ")";
                        }
                    }

                    PedidoHistorial::create([
                        'pedido_id' => $id,
                        'usuario_id' => $usuario_id,
                        'accion' => 'reasignacion',
                        'detalles' => $detalles_historial,
                        'fecha' => now()
                    ]);
                }
            }

            DB::commit();

            // Actualizar contadores para el sidebar
            $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')
                ->where('sucursal_id', $this->sucursalId)
                ->count();
                
            $productos_bajos_count = DB::table('producto_sucursal')
                ->where('sucursal_id', $this->sucursalId)
                ->where('existencias', '<=', 5)
                ->distinct('producto_id')
                ->count('producto_id');

            session([
                'pedidos_pendientes_count' => $pedidos_pendientes_count,
                'productos_bajos_count' => $productos_bajos_count
            ]);

            return redirect()->route('gerente.pedidos.editar', $id)
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Actualizado!',
                    'message' => 'Pedido actualizado correctamente en tu sucursal'
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar pedido: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al actualizar el pedido: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * ✅ MÉTODO MEJORADO: Procesa acciones rápidas sobre el pedido (con stock seguro)
     */
    public function procesarAccion($accion, $id)
    {
        // Obtener pedido - SOLO si pertenece a la sucursal del gerente
        $pedido = Pedido::with('items')
            ->where('sucursal_id', $this->sucursalId)
            ->where('id', $id)
            ->first();

        if (!$pedido) {
            return redirect()->route('gerente.pedidos')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Pedido no encontrado en tu sucursal'
                ]);
        }

        $usuario_id = auth()->id();

        try {
            DB::beginTransaction();

            switch ($accion) {
                case 'cancelar':
                    // ✅ REGRESAR STOCK SEGURO (solo si no está regresado)
                    if ($pedido->estado != 'cancelado') {
                        $this->regresarStockSeguro($pedido);
                    }
                    $pedido->estado = 'cancelado';
                    $mensaje = 'Pedido cancelado correctamente';
                    $accion_historial = 'cancelacion';
                    $detalles = 'Pedido cancelado por gerente';
                    break;
                    
                case 'confirmar_pago':
                    $pedido->pago_confirmado = true;
                    $pedido->fecha_confirmacion = now();
                    $mensaje = 'Pago confirmado correctamente';
                    $accion_historial = 'confirmacion_pago';
                    $detalles = 'Pago confirmado por gerente';
                    break;
                    
                case 'desconfirmar_pago':
                    $pedido->pago_confirmado = false;
                    $mensaje = 'Pago desconfirmado correctamente';
                    $accion_historial = 'desconfirmacion_pago';
                    $detalles = 'Pago desconfirmado por gerente';
                    break;
                    
                case 'confirmar':
                    // ✅ SI VIENE DE CANCELADO, DESCONTAR STOCK SEGURO
                    if ($pedido->estado == 'cancelado') {
                        $this->descontarStockSeguro($pedido);
                    }
                    $pedido->estado = 'confirmado';
                    $mensaje = 'Pedido confirmado correctamente';
                    $accion_historial = 'confirmacion';
                    $detalles = 'Pedido confirmado por gerente';
                    break;
                    
                case 'enviar':
                    $pedido->estado = 'enviado';
                    $mensaje = 'Pedido marcado como enviado';
                    $accion_historial = 'envio';
                    $detalles = 'Pedido marcado como enviado por gerente';
                    break;
                    
                case 'entregar':
                    $pedido->estado = 'entregado';
                    $pedido->fecha_entrega = now();
                    $mensaje = 'Pedido marcado como entregado';
                    $accion_historial = 'entrega';
                    $detalles = 'Pedido marcado como entregado por gerente';
                    break;
                    
                case 'tomar_control':
                    // Eliminar asignaciones anteriores
                    DB::table('pedido_responsables')
                        ->where('pedido_id', $id)
                        ->delete();

                    // Asignar gerente como responsable
                    DB::table('pedido_responsables')->insert([
                        'pedido_id' => $id,
                        'usuario_id' => $usuario_id,
                        'fecha_asignacion' => now()
                    ]);

                    $mensaje = 'Has tomado control del pedido';
                    $accion_historial = 'toma_control';
                    $detalles = 'Gerente tomó control del pedido';
                    break;
                    
                default:
                    return redirect()->back()->with('swal', [
                        'type' => 'error',
                        'title' => 'Error',
                        'message' => 'Acción no válida'
                    ]);
            }

            $pedido->save();

            PedidoHistorial::create([
                'pedido_id' => $id,
                'usuario_id' => $usuario_id,
                'accion' => $accion_historial,
                'detalles' => $detalles,
                'fecha' => now()
            ]);

            DB::commit();

            // Actualizar contadores
            $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')
                ->where('sucursal_id', $this->sucursalId)
                ->count();
                
            $productos_bajos_count = DB::table('producto_sucursal')
                ->where('sucursal_id', $this->sucursalId)
                ->where('existencias', '<=', 5)
                ->distinct('producto_id')
                ->count('producto_id');

            session([
                'pedidos_pendientes_count' => $pedidos_pendientes_count,
                'productos_bajos_count' => $productos_bajos_count
            ]);

            return redirect()->route('gerente.pedidos.ver', $id)
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Acción completada!',
                    'message' => $mensaje
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar acción: ' . $e->getMessage());

            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al procesar la acción: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ✅ MÉTODO MEJORADO: Eliminar un pedido permanentemente (con stock seguro)
     */
    public function destroy($id)
    {
        // Obtener pedido - SOLO si pertenece a la sucursal del gerente
        $pedido = Pedido::with('items')
            ->where('sucursal_id', $this->sucursalId)
            ->where('id', $id)
            ->first();

        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado en tu sucursal'
            ], 404);
        }

        $folio = $pedido->folio;
        
        try {
            DB::beginTransaction();
            
            // ✅ REGRESAR STOCK SEGURO (solo si no está regresado y no está cancelado)
            if ($pedido->estado != 'cancelado' && !$this->tieneStockRegresado($pedido)) {
                $this->regresarStockSeguro($pedido);
            }
            
            // Eliminar items relacionados
            PedidoItem::where('pedido_id', $id)->delete();
            
            // Eliminar historial
            PedidoHistorial::where('pedido_id', $id)->delete();
            
            // Eliminar responsables
            DB::table('pedido_responsables')->where('pedido_id', $id)->delete();
            
            // Eliminar pedido
            $pedido->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "El pedido #{$folio} ha sido eliminado correctamente."
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar pedido: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asigna un responsable al pedido (API AJAX)
     */
    public function asignarResponsable(Request $request)
    {
        $pedido_id = $request->get('pedido_id');
        $usuario_id = $request->get('usuario_id');
        
        if (!$pedido_id || !$usuario_id) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }
        
        try {
            // Verificar que el pedido pertenece a la sucursal
            $pedido = Pedido::where('id', $pedido_id)
                ->where('sucursal_id', $this->sucursalId)
                ->first();

            if (!$pedido) {
                return response()->json(['error' => 'Pedido no encontrado en tu sucursal'], 404);
            }

            $auth_usuario_id = auth()->id();
            
            DB::table('pedido_responsables')->updateOrInsert(
                ['pedido_id' => $pedido_id],
                [
                    'usuario_id' => $usuario_id,
                    'fecha_asignacion' => now()
                ]
            );
            
            $usuario = Usuario::find($usuario_id);
            PedidoHistorial::create([
                'pedido_id' => $pedido_id,
                'usuario_id' => $auth_usuario_id,
                'accion' => 'responsable_asignado',
                'detalles' => "Responsable asignado: {$usuario->nombre}",
                'fecha' => now()
            ]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error al asignar responsable: ' . $e->getMessage());
            return response()->json(['error' => 'Error al asignar responsable'], 500);
        }
    }

    /**
     * Remueve el responsable del pedido (API AJAX)
     */
    public function removerResponsable(Request $request)
    {
        $pedido_id = $request->get('pedido_id');
        
        if (!$pedido_id) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }
        
        try {
            // Verificar que el pedido pertenece a la sucursal
            $pedido = Pedido::where('id', $pedido_id)
                ->where('sucursal_id', $this->sucursalId)
                ->first();

            if (!$pedido) {
                return response()->json(['error' => 'Pedido no encontrado en tu sucursal'], 404);
            }

            $auth_usuario_id = auth()->id();
            
            DB::table('pedido_responsables')
                ->where('pedido_id', $pedido_id)
                ->delete();
            
            PedidoHistorial::create([
                'pedido_id' => $pedido_id,
                'usuario_id' => $auth_usuario_id,
                'accion' => 'responsable_removido',
                'detalles' => 'Responsable removido del pedido',
                'fecha' => now()
            ]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error al remover responsable: ' . $e->getMessage());
            return response()->json(['error' => 'Error al remover responsable'], 500);
        }
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
    public function buscar(Request $request)
    {
        $busqueda = $request->get('busqueda');
        
        if (empty($busqueda) || strlen($busqueda) < 3) {
            return response()->json([]);
        }
        
        $clientes = Cliente::where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('telefono', 'LIKE', "%{$busqueda}%")
                    ->orWhere('email', 'LIKE', "%{$busqueda}%")
                    ->limit(10)
                    ->get(['id', 'nombre', 'telefono', 'email', 'direccion', 'ciudad', 'estado', 'codigo_postal']);
        
        return response()->json($clientes);
    }

}