<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoriaController extends Controller
{
    public function index()
    {
        // Contadores para el sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')->count();
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        // Obtener categorías con conteo de productos
        $categorias = Categoria::withCount(['productos' => function($query) {
            $query->where('activo', true);
        }])->orderBy('nombre')->get();

        return view('admin.categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre'
        ]);

        try {
            Categoria::create([
                'nombre' => trim($request->nombre)
            ]);

            return redirect()->route('admin.categorias')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Éxito!',
                    'message' => 'Categoría creada correctamente'
                ]);

        } catch (\Exception $e) {
            Log::error('Error al crear categoría: ' . $e->getMessage());
            
            return redirect()->route('admin.categorias')
                ->with('swal', [
                    'type' => 'error',
                    'title' => '¡Error!',
                    'message' => 'Error al crear la categoría'
                ]);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:100|unique:categorias,nombre,' . $request->id
        ]);

        try {
            $categoria = Categoria::findOrFail($request->id);
            $categoria->update([
                'nombre' => trim($request->nombre)
            ]);

            return redirect()->route('admin.categorias')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Éxito!',
                    'message' => 'Categoría actualizada correctamente'
                ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar categoría: ' . $e->getMessage());
            
            return redirect()->route('admin.categorias')
                ->with('swal', [
                    'type' => 'error',
                    'title' => '¡Error!',
                    'message' => 'Error al actualizar la categoría'
                ]);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:categorias,id'
        ]);

        try {
            $categoria = Categoria::findOrFail($request->id);
            
            // Verificar si tiene productos asociados
            $productos_count = Producto::where('categoria_id', $categoria->id)
                ->where('activo', true)
                ->count();

            if ($productos_count > 0) {
                return redirect()->route('admin.categorias')
                    ->with('swal', [
                        'type' => 'error',
                        'title' => 'No se puede eliminar',
                        'message' => "La categoría tiene {$productos_count} productos asociados"
                    ]);
            }

            $categoria->delete();

            return redirect()->route('admin.categorias')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Éxito!',
                    'message' => 'Categoría eliminada correctamente'
                ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar categoría: ' . $e->getMessage());
            
            return redirect()->route('admin.categorias')
                ->with('swal', [
                    'type' => 'error',
                    'title' => '¡Error!',
                    'message' => 'Error al eliminar la categoría'
                ]);
        }
    }
}