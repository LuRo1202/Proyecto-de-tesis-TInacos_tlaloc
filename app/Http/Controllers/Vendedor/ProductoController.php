<?php
namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use App\Models\Categoria;
use App\Helpers\ProductoHelper;
use App\Models\Oferta;

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

        // Obtener producto con ofertas activas
        $producto = Producto::with(['categoria', 'color', 'ofertas' => function($query) {
                $query->where('activa', 1)
                      ->where('fecha_inicio', '<=', now())
                      ->where('fecha_fin', '>=', now());
            }])
            ->where('id', $id)
            ->where('activo', 1)
            ->first();

        if (!$producto) {
            return redirect()->route('vendedor.catalogo.index')
                ->with('error', 'Producto no encontrado.');
        }

        // Calcular precio con oferta si existe
        $precio_original = $producto->precio;
        $precio_final = $producto->precio;
        $porcentaje_descuento = 0;
        $en_oferta = false;

        if ($producto->ofertas->isNotEmpty()) {
            $oferta = $producto->ofertas->first();
            $en_oferta = true;
            
            if ($oferta->tipo == 'porcentaje') {
                $precio_final = $producto->precio * (1 - $oferta->valor / 100);
                $porcentaje_descuento = $oferta->valor;
            } else {
                $precio_final = $producto->precio - $oferta->valor;
                $porcentaje_descuento = round(($oferta->valor / $producto->precio) * 100);
            }
        }

        // Agregar propiedades al producto (opcional pero útil)
        $producto->en_oferta = $en_oferta;
        $producto->porcentaje_descuento = $porcentaje_descuento;
        $producto->precio_original = $precio_original;
        $producto->precio_final = $precio_final;

        // Obtener existencias en esta sucursal
        $existencias = $sucursal->productos()
            ->where('producto_id', $producto->id)
            ->first()
            ->pivot
            ->existencias ?? 0;

        // Obtener variantes del producto con sus ofertas
        $codigoBase = $producto->codigo;
        
        if (strpos($codigoBase, 'DISP-20') === 0) {
            $familia = 'DISP-20';
            $variantes = Producto::where('codigo', 'LIKE', 'DISP-20%')
                ->where('activo', 1)
                ->with(['color', 'categoria', 'ofertas' => function($query) {
                    $query->where('activa', 1)
                          ->where('fecha_inicio', '<=', now())
                          ->where('fecha_fin', '>=', now());
                }])
                ->get();
        } 
        elseif (strpos($codigoBase, 'ACC-') === 0) {
            $variantes = collect([$producto]);
        } 
        else {
            $familia = preg_replace('/-(C|N|R|AZ|RM|M|B|MD)$/i', '', $codigoBase);
            $variantes = Producto::where('codigo', 'LIKE', $familia . '%')
                ->where('activo', 1)
                ->with(['color', 'categoria', 'ofertas' => function($query) {
                    $query->where('activa', 1)
                          ->where('fecha_inicio', '<=', now())
                          ->where('fecha_fin', '>=', now());
                }])
                ->get();
        }

        // Procesar variantes
        foreach ($variantes as $variante) {
            $variante->en_oferta = false;
            $variante->porcentaje_descuento = 0;
            $variante->precio_original = $variante->precio;
            $variante->precio_final = $variante->precio;
            
            if ($variante->ofertas->isNotEmpty()) {
                $ofertaVar = $variante->ofertas->first();
                $variante->en_oferta = true;
                
                if ($ofertaVar->tipo == 'porcentaje') {
                    $variante->precio_final = $variante->precio * (1 - $ofertaVar->valor / 100);
                    $variante->porcentaje_descuento = $ofertaVar->valor;
                } else {
                    $variante->precio_final = $variante->precio - $ofertaVar->valor;
                    $variante->porcentaje_descuento = round(($ofertaVar->valor / $variante->precio) * 100);
                }
            }
        }

        // Asegurar que el producto actual esté en la lista de variantes
        if (!$variantes->contains('id', $producto->id)) {
            $variantes = $variantes->push($producto);
        }

        return view('vendedor.producto.detalles', compact(
            'producto',
            'variantes',
            'existencias',
            'sucursal',
            'en_oferta',
            'precio_original',
            'precio_final',
            'porcentaje_descuento'
        ));
    }
}