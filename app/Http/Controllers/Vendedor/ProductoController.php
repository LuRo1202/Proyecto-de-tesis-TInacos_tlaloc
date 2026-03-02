<?php
namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use App\Models\Categoria;
use App\Helpers\ProductoHelper;

class ProductoController extends Controller
{
    public function detalles($id)
    {
        $usuario = Auth::user();
        
        // Obtener sucursal del vendedor
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }

        // Obtener producto
        $producto = Producto::with(['categoria', 'color'])
            ->where('id', $id)
            ->where('activo', 1)
            ->first();

        if (!$producto) {
            return redirect()->route('vendedor.catalogo.index')
                ->with('error', 'Producto no encontrado.');
        }

        // Obtener existencias en esta sucursal
        $existencias = $sucursal->productos()
            ->where('producto_id', $producto->id)
            ->first()
            ->pivot
            ->existencias ?? 0;

        // Obtener variantes del producto (todos los productos de la misma familia)
        $codigoBase = $producto->codigo;
        
        // Si es DISP-20, buscar todas las variantes de DISP-20
        if (strpos($codigoBase, 'DISP-20') === 0) {
            $familia = 'DISP-20';
            $variantes = Producto::where('codigo', 'LIKE', 'DISP-20%')
                ->where('activo', 1)
                ->with(['color', 'categoria'])
                ->get();
        } 
        // Si es accesorio, no tiene variantes
        elseif (strpos($codigoBase, 'ACC-') === 0) {
            $variantes = collect([$producto]);
        } 
        // Otros productos, buscar por familia base
        else {
            $familia = preg_replace('/-(C|N|R|AZ|RM|M|B|MD)$/i', '', $codigoBase);
            $variantes = Producto::where('codigo', 'LIKE', $familia . '%')
                ->where('activo', 1)
                ->with(['color', 'categoria'])
                ->get();
        }

        // Asegurar que el producto actual esté en la lista de variantes
        if (!$variantes->contains('id', $producto->id)) {
            $variantes = $variantes->push($producto);
        }

        return view('vendedor.producto.detalles', compact(
            'producto',
            'variantes',
            'existencias',
            'sucursal'
        ));
    }
}