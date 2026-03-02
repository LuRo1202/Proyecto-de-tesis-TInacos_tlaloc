<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Oferta;
use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfertaController extends Controller
{
    public function index(Request $request)
    {
        // Contadores para sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')->count();
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        // Filtros
        $query = Oferta::withCount('productos');

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

        return view('admin.ofertas.index', compact('ofertas'));
    }

    public function create()
    {
        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->groupBy(function($producto) {
                return $producto->categoria->nombre ?? 'Sin categoría';
            });

        return view('admin.ofertas.create', compact('productos'));
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

            return redirect()->route('admin.ofertas')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Oferta creada!',
                    'message' => "La oferta '{$oferta->nombre}' se ha creado correctamente."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
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
        
        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->groupBy(function($producto) {
                return $producto->categoria->nombre ?? 'Sin categoría';
            });

        $productosSeleccionados = $oferta->productos->pluck('id')->toArray();

        return view('admin.ofertas.edit', compact('oferta', 'productos', 'productosSeleccionados'));
    }

    public function update(Request $request, $id)
    {
        $oferta = Oferta::findOrFail($id);

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

            return redirect()->route('admin.ofertas')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Oferta actualizada!',
                    'message' => "La oferta '{$oferta->nombre}' se ha actualizado correctamente."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al actualizar la oferta: ' . $e->getMessage()
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $oferta = Oferta::findOrFail($id);
            $nombre = $oferta->nombre;
            
            DB::beginTransaction();
            $oferta->productos()->detach();
            $oferta->delete();
            DB::commit();

            return redirect()->route('admin.ofertas')
                ->with('swal', [
                    'type' => 'success',
                    'title' => 'Oferta eliminada',
                    'message' => "La oferta '{$nombre}' se ha eliminado correctamente."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al eliminar la oferta: ' . $e->getMessage()
            ]);
        }
    }

    public function toggle($id)
    {
        try {
            $oferta = Oferta::findOrFail($id);
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