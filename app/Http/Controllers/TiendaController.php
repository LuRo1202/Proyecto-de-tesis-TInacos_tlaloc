<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Helpers\SucursalHelper;
use App\Helpers\ProductoHelper;
use App\Helpers\CarritoHelper;

class TiendaController extends Controller
{
    public function index(Request $request)
    {
        // Obtener sucursal actual
        $sucursal = SucursalHelper::getSucursalActual();
        
        // Obtener parámetros
        $categoria_id = $request->get('categoria', 0);
        $busqueda = $request->get('q', '');
        $precio_min = $request->get('precio_min', 0);
        $precio_max = $request->get('precio_max', 0);

        // Limpiar búsqueda
        if (!empty($busqueda)) {
            $busqueda = strip_tags($busqueda);
            $busqueda = preg_replace('/[^\p{L}\p{N}\s\-\.]/u', '', $busqueda);
        }

        // Obtener todas las categorías
        $categorias = Categoria::all();

        // Obtener productos con stock de la sucursal
        $productosQuery = $sucursal->productos()
            ->wherePivot('existencias', '>', 0)
            ->withPivot('existencias')
            ->with(['categoria', 'color', 'ofertas']);

        // Aplicar filtros
        if ($categoria_id > 0) {
            $productosQuery->where('categoria_id', $categoria_id);
            $categoria = $categorias->firstWhere('id', $categoria_id);
            $tituloCategoria = $categoria ? $categoria->nombre : 'Categoría';
        } elseif (!empty($busqueda)) {
            $productosQuery->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('codigo', 'LIKE', "%{$busqueda}%");
            });
            $tituloCategoria = "Resultados de búsqueda: '$busqueda'";
        } else {
            $tituloCategoria = "Todos los productos";
        }

        // Obtener productos
        $productos = $productosQuery->get();

        // Filtrar por precio
        if ($precio_min > 0 || $precio_max > 0) {
            $productos = $productos->filter(function($producto) use ($precio_min, $precio_max) {
                if ($precio_min > 0 && $producto->precio < $precio_min) return false;
                if ($precio_max > 0 && $producto->precio > $precio_max) return false;
                return true;
            });
            
            $tituloCategoria .= " - $" . number_format($precio_min) . " - $" . number_format($precio_max);
        }

        // AGRUPACIÓN DE PRODUCTOS
        $productosAgrupados = [];
        foreach ($productos as $producto) {
            $codigo = $producto->codigo;
            
            // 1. ACCESORIOS - No se agrupan
            if (strpos($codigo, 'ACC-') === 0) {
                $familia = $codigo;
            } 
            // 2. DISPENSADORES - Todos en UNA sola familia
            elseif (strpos($codigo, 'DISP-20') === 0) {
                $familia = 'DISP-20';
            }
            // 3. OTROS PRODUCTOS - Extraer base quitando cualquier sufijo
            else {
                // Quitar cualquier sufijo como -C, -N, -R, -AZ, -RM, -M, -B, -MD
                $familia = preg_replace('/-(C|N|R|AZ|RM|M|B|MD)$/i', '', $codigo);
            }
            
            // Inicializar si no existe
            if (!isset($productosAgrupados[$familia])) {
                $productosAgrupados[$familia] = [
                    'variantes' => [],
                    'principal' => null
                ];
            }
            
            // Agregar variante
            $productosAgrupados[$familia]['variantes'][] = $producto;
            
            // Determinar producto principal
            if ($familia === 'DISP-20' && $codigo === 'DISP-20') {
                $productosAgrupados[$familia]['principal'] = $producto;
            } 
            elseif ($familia !== 'DISP-20' && !preg_match('/-(C|N|R|AZ|RM|M|B)$/i', $codigo)) {
                $productosAgrupados[$familia]['principal'] = $producto;
            }
        }

        // Asegurar que todas las familias tengan producto principal y enriquecer datos
        foreach ($productosAgrupados as $familia => &$datos) {
            if ($datos['principal'] === null && count($datos['variantes']) > 0) {
                $datos['principal'] = $datos['variantes'][0];
            }
            
            // Enriquecer variantes con datos para JS
            foreach ($datos['variantes'] as &$variante) {
                $info = ProductoHelper::obtenerInfoVariante($variante);
                $variante->imagen = ProductoHelper::obtenerImagenProducto($variante->codigo);
                $variante->color_nombre = $info['nombre'];
                $variante->color_hex = $info['hex'];
                $variante->precio_formateado = ProductoHelper::formatoPrecio($variante->precio);
                $variante->en_oferta = $variante->en_oferta;
                $variante->precio_final = $variante->precio_final;
                $variante->porcentaje_descuento = $variante->porcentaje_descuento;
            }
            
            // Ordenar variantes: primero las que tienen stock
            usort($datos['variantes'], function($a, $b) {
                $stockA = $a->pivot->existencias > 0 ? 1 : 0;
                $stockB = $b->pivot->existencias > 0 ? 1 : 0;
                return $stockB <=> $stockA;
            });
        }

        // ✅ CÁLCULO CORRECTO DEL CART COUNT USANDO EL HELPER
        $cartCount = CarritoHelper::getCartCount();

        return view('tienda', compact(
            'productosAgrupados',
            'categorias',
            'categoria_id',
            'busqueda',
            'precio_min',
            'precio_max',
            'tituloCategoria',
            'cartCount',
            'sucursal'
        ));
    }
}