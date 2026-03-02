<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Helpers\SucursalHelper;
use App\Helpers\ProductoHelper;

class ProductoController extends Controller
{
    public function show($id)
    {
        // Obtener sucursal actual
        $sucursal = SucursalHelper::getSucursalActual();
        
        // Buscar producto con todas sus relaciones (incluyendo ofertas)
        $producto = Producto::with(['categoria', 'color', 'ofertas'])
            ->where('activo', true)
            ->findOrFail($id);
            
        // Verificar que el producto tenga stock en esta sucursal
        $productoConStock = $sucursal->productos()
            ->where('producto_id', $producto->id)
            ->withPivot('existencias')
            ->first();
            
        if (!$productoConStock) {
            abort(404, 'Producto no disponible en esta sucursal');
        }
        
        // Asignar las existencias al producto
        $producto->pivot = (object) [
            'existencias' => $productoConStock->pivot->existencias
        ];
        
        // Calcular propiedades de oferta
        $producto->precio_original = $producto->precio;
        $producto->precio_final = $producto->precio_final;
        $producto->en_oferta = $producto->en_oferta;
        $producto->porcentaje_descuento = $producto->porcentaje_descuento;
        
        // Obtener todas las variantes de este producto
        $codigoBase = $producto->codigo;
        
        if (strpos($codigoBase, 'DISP-20') === 0) {
            $familia = 'DISP-20';
        } 
        elseif (strpos($codigoBase, 'ACC-') === 0) {
            $familia = $codigoBase;
        } 
        else {
            $familia = preg_replace('/-(C|N|R|AZ|RM|M|B|MD)$/i', '', $codigoBase);
        }
        
        $variantesQuery = $sucursal->productos()
            ->where('activo', true)
            ->withPivot('existencias')
            ->with(['color', 'ofertas']);
            
        if ($familia === 'DISP-20') {
            $variantesQuery->where('codigo', 'LIKE', 'DISP-20%');
        } elseif (strpos($codigoBase, 'ACC-') === 0) {
            $variantesQuery->where('codigo', $codigoBase);
        } else {
            $variantesQuery->where('codigo', 'LIKE', $familia . '%');
        }
        
        $variantes = $variantesQuery->get();
        
        // Enriquecer variantes con datos de ofertas
        foreach ($variantes as $variante) {
            $variante->precio_original = $variante->precio;
            $variante->precio_final = $variante->precio_final;
            $variante->en_oferta = $variante->en_oferta;
            $variante->porcentaje_descuento = $variante->porcentaje_descuento;
        }
        
        $variantes = $variantes->sortByDesc(function($v) {
            return $v->pivot->existencias > 0;
        })->sortBy('codigo');
        
        $cart = session()->get('carrito', []);
        $cartCount = array_sum(array_column($cart, 'cantidad'));
        
        return view('producto-detalle', compact(
            'producto',
            'variantes',
            'sucursal',
            'cartCount'
        ));
    }
}