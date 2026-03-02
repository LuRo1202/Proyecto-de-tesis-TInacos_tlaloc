<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Oferta;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OfertaController extends Controller
{
    protected $sucursal_id;

    public function __construct()
    {
        // Obtener la sucursal del gerente desde la sesión
        $this->sucursal_id = session('sucursal_id');
        
        if (!$this->sucursal_id) {
            $usuario = auth()->user();
            if ($usuario && $usuario->sucursales()->exists()) {
                $this->sucursal_id = $usuario->sucursales()->first()->id;
                session(['sucursal_id' => $this->sucursal_id]);
                session(['sucursal_nombre' => $usuario->sucursales()->first()->nombre]);
            }
        }
    }

    public function index(Request $request)
    {
        // Contadores para sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')
            ->where('sucursal_id', $this->sucursal_id)
            ->count();
            
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursal_id)
            ->where('existencias', '<=', 5)
            ->count();

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        // Obtener productos de la sucursal del gerente
        $productosIds = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursal_id)
            ->pluck('producto_id');

        // Query de ofertas
        $query = Oferta::withCount('productos')
            ->whereHas('productos', function($q) use ($productosIds) {
                $q->whereIn('productos.id', $productosIds);
            });

        // Filtros
        if ($request->filled('estado')) {
            if ($request->estado === 'activas') {
                $query->where('activa', true);
            } elseif ($request->estado === 'inactivas') {
                $query->where('activa', false);
            }
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $ofertas = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('gerente.ofertas.index', compact('ofertas'));
    }

    public function create()
    {
        // Solo productos de su sucursal
        $productos = Producto::where('activo', true)
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursal_id);
            })
            ->with(['categoria', 'sucursales' => function($q) {
                $q->where('sucursal_id', $this->sucursal_id);
            }])
            ->orderBy('nombre')
            ->get()
            ->groupBy(function($producto) {
                return $producto->categoria->nombre ?? 'Sin categoría';
            });

        return view('gerente.ofertas.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:200',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:porcentaje,fijo',
            'valor' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'productos' => 'required|array|min:1',
            'productos.*' => 'exists:productos,id'
        ], [
            'productos.required' => 'Debes seleccionar al menos un producto',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio'
        ]);

        // Verificar que los productos pertenezcan a su sucursal
        $productosValidos = DB::table('producto_sucursal')
            ->whereIn('producto_id', $request->productos)
            ->where('sucursal_id', $this->sucursal_id)
            ->count();

        if ($productosValidos != count($request->productos)) {
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Solo puedes agregar productos de tu sucursal'
                ]);
        }

        try {
            DB::beginTransaction();

            $oferta = Oferta::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'valor' => $request->valor,
                'fecha_inicio' => Carbon::parse($request->fecha_inicio)->startOfDay(),
                'fecha_fin' => Carbon::parse($request->fecha_fin)->endOfDay(),
                'activa' => $request->has('activa')
            ]);

            // Calcular precio de oferta para cada producto
            $productosData = [];
            foreach ($request->productos as $productoId) {
                $producto = Producto::find($productoId);
                if ($producto) {
                    if ($request->tipo === 'porcentaje') {
                        $precioOferta = $producto->precio * (1 - $request->valor / 100);
                    } else {
                        $precioOferta = $producto->precio - $request->valor;
                    }
                    $productosData[$productoId] = ['precio_oferta' => max(0, $precioOferta)];
                }
            }

            $oferta->productos()->attach($productosData);

            DB::commit();

            return redirect()->route('gerente.ofertas')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Oferta creada!',
                    'message' => "La oferta '{$oferta->nombre}' se ha creado correctamente."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear oferta (gerente): ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al crear la oferta: ' . $e->getMessage()
                ]);
        }
    }

    public function edit($id)
    {
        $oferta = Oferta::with('productos')->findOrFail($id);
        
        // Verificar que la oferta incluya productos de su sucursal
        $productosSucursal = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursal_id)
            ->pluck('producto_id')
            ->toArray();

        $productosOferta = $oferta->productos->pluck('id')->toArray();
        $interseccion = array_intersect($productosOferta, $productosSucursal);

        if (empty($interseccion)) {
            return redirect()->route('gerente.ofertas')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Acceso denegado',
                    'message' => 'No tienes permisos para editar esta oferta'
                ]);
        }

        // Solo productos de su sucursal
        $productos = Producto::where('activo', true)
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursal_id);
            })
            ->with(['categoria'])
            ->orderBy('nombre')
            ->get()
            ->groupBy(function($producto) {
                return $producto->categoria->nombre ?? 'Sin categoría';
            });

        $productosSeleccionados = array_intersect($productosOferta, $productosSucursal);

        return view('gerente.ofertas.edit', compact('oferta', 'productos', 'productosSeleccionados'));
    }

    public function update(Request $request, $id)
    {
        $oferta = Oferta::with('productos')->findOrFail($id);

        // Verificar permisos
        $productosSucursal = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursal_id)
            ->pluck('producto_id')
            ->toArray();

        $productosOferta = $oferta->productos->pluck('id')->toArray();
        if (empty(array_intersect($productosOferta, $productosSucursal))) {
            return redirect()->route('gerente.ofertas')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Acceso denegado',
                    'message' => 'No tienes permisos para editar esta oferta'
                ]);
        }

        $request->validate([
            'nombre' => 'required|max:200',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:porcentaje,fijo',
            'valor' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'productos' => 'required|array|min:1',
            'productos.*' => 'exists:productos,id'
        ]);

        // Verificar que los productos seleccionados sean de su sucursal
        $productosValidos = DB::table('producto_sucursal')
            ->whereIn('producto_id', $request->productos)
            ->where('sucursal_id', $this->sucursal_id)
            ->count();

        if ($productosValidos != count($request->productos)) {
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Solo puedes seleccionar productos de tu sucursal'
                ]);
        }

        try {
            DB::beginTransaction();

            $oferta->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'valor' => $request->valor,
                'fecha_inicio' => Carbon::parse($request->fecha_inicio)->startOfDay(),
                'fecha_fin' => Carbon::parse($request->fecha_fin)->endOfDay(),
                'activa' => $request->has('activa')
            ]);

            // Recalcular precios de oferta
            $productosData = [];
            foreach ($request->productos as $productoId) {
                $producto = Producto::find($productoId);
                if ($producto) {
                    if ($request->tipo === 'porcentaje') {
                        $precioOferta = $producto->precio * (1 - $request->valor / 100);
                    } else {
                        $precioOferta = $producto->precio - $request->valor;
                    }
                    $productosData[$productoId] = ['precio_oferta' => max(0, $precioOferta)];
                }
            }

            $oferta->productos()->sync($productosData);

            DB::commit();

            return redirect()->route('gerente.ofertas')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Oferta actualizada!',
                    'message' => "La oferta '{$oferta->nombre}' se ha actualizado correctamente."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar oferta (gerente): ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al actualizar la oferta: ' . $e->getMessage()
                ]);
        }
    }

    public function toggle($id)
    {
        $oferta = Oferta::with('productos')->findOrFail($id);

        // Verificar permisos
        $productosSucursal = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursal_id)
            ->pluck('producto_id')
            ->toArray();

        $productosOferta = $oferta->productos->pluck('id')->toArray();
        if (empty(array_intersect($productosOferta, $productosSucursal))) {
            return redirect()->route('gerente.ofertas')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Acceso denegado',
                    'message' => 'No tienes permisos para modificar esta oferta'
                ]);
        }

        try {
            $oferta->activa = !$oferta->activa;
            $oferta->save();

            return redirect()->back()->with('swal', [
                'type' => 'success',
                'title' => 'Estado actualizado',
                'message' => "La oferta se ha " . ($oferta->activa ? 'activado' : 'desactivado') . " correctamente."
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ]);
        }
    }
}