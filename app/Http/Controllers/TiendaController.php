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
        $soloOfertas = $request->get('oferta', 0); // 👈 NUEVO: Filtro de ofertas

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

        // ===== NUEVO: FILTRO POR OFERTAS (sin afectar la lógica existente) =====
        if ($soloOfertas == 1) {
            $productosQuery->whereHas('ofertas', function($query) {
                $query->where('activa', 1)
                      ->where('fecha_inicio', '<=', now())
                      ->where('fecha_fin', '>=', now());
            });
            $tituloCategoria = "🔥 Productos en Oferta";
        }
        // ====================================================================

        // Aplicar filtros de categoría
        if ($categoria_id > 0) {
            $productosQuery->where('categoria_id', $categoria_id);
            // Solo cambiar título si NO es filtro de ofertas
            if ($soloOfertas != 1) {
                $categoria = $categorias->firstWhere('id', $categoria_id);
                $tituloCategoria = $categoria ? $categoria->nombre : 'Categoría';
            }
        } 
        // Aplicar filtros de búsqueda
        elseif (!empty($busqueda)) {
            $productosQuery->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('codigo', 'LIKE', "%{$busqueda}%");
            });
            // Solo cambiar título si NO es filtro de ofertas
            if ($soloOfertas != 1) {
                $tituloCategoria = "Resultados de búsqueda: '$busqueda'";
            }
        } 
        // Título por defecto
        elseif ($soloOfertas != 1) {
            $tituloCategoria = "Todos los productos";
        }

        // Obtener productos
        $productos = $productosQuery->get();

        // ===== ENRIQUECER PRODUCTOS CON DATOS DE OFERTA =====
        foreach ($productos as $producto) {
            $ofertaActiva = $producto->ofertas->first();
            
            if ($ofertaActiva) {
                $producto->en_oferta = true;
                
                if ($ofertaActiva->tipo == 'porcentaje') {
                    $producto->precio_original = $producto->precio;
                    $producto->precio_final = $producto->precio * (1 - $ofertaActiva->valor / 100);
                    $producto->porcentaje_descuento = $ofertaActiva->valor;
                } else {
                    $producto->precio_original = $producto->precio;
                    $producto->precio_final = $producto->precio - $ofertaActiva->valor;
                    $producto->porcentaje_descuento = round(($ofertaActiva->valor / $producto->precio) * 100);
                }
            } else {
                $producto->en_oferta = false;
                $producto->precio_original = $producto->precio;
                $producto->precio_final = $producto->precio;
                $producto->porcentaje_descuento = 0;
            }
        }
        // ===================================================

        // Filtrar por precio (DESPUÉS de tener los precios finales con oferta)
        if ($precio_min > 0 || $precio_max > 0) {
            $productos = $productos->filter(function($producto) use ($precio_min, $precio_max) {
                $precioComparar = $producto->precio_final ?? $producto->precio;
                if ($precio_min > 0 && $precioComparar < $precio_min) return false;
                if ($precio_max > 0 && $precioComparar > $precio_max) return false;
                return true;
            });
            
            // Agregar al título solo si NO es filtro de ofertas
            if ($soloOfertas != 1) {
                $tituloCategoria .= " - $" . number_format($precio_min) . " - $" . number_format($precio_max);
            }
        }

        // AGRUPACIÓN DE PRODUCTOS (sin cambios)
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
                $familia = preg_replace('/-(C|N|R|AZ|RM|M|B|MD)$/i', '', $codigo);
            }
            
            if (!isset($productosAgrupados[$familia])) {
                $productosAgrupados[$familia] = [
                    'variantes' => [],
                    'principal' => null
                ];
            }
            
            $productosAgrupados[$familia]['variantes'][] = $producto;
            
            if ($familia === 'DISP-20' && $codigo === 'DISP-20') {
                $productosAgrupados[$familia]['principal'] = $producto;
            } 
            elseif ($familia !== 'DISP-20' && !preg_match('/-(C|N|R|AZ|RM|M|B)$/i', $codigo)) {
                $productosAgrupados[$familia]['principal'] = $producto;
            }
        }

        // Asegurar que todas las familias tengan producto principal
        foreach ($productosAgrupados as $familia => &$datos) {
            if ($datos['principal'] === null && count($datos['variantes']) > 0) {
                $datos['principal'] = $datos['variantes'][0];
            }
            
            foreach ($datos['variantes'] as &$variante) {
                $info = ProductoHelper::obtenerInfoVariante($variante);
                $variante->imagen = ProductoHelper::obtenerImagenProducto($variante->codigo);
                $variante->color_nombre = $info['nombre'];
                $variante->color_hex = $info['hex'];
                $variante->precio_formateado = ProductoHelper::formatoPrecio($variante->precio);
            }
            
            usort($datos['variantes'], function($a, $b) {
                $stockA = $a->pivot->existencias > 0 ? 1 : 0;
                $stockB = $b->pivot->existencias > 0 ? 1 : 0;
                return $stockB <=> $stockA;
            });
        }

        $cartCount = CarritoHelper::getCartCount();

        return view('tienda', compact(
            'productosAgrupados',
            'categorias',
            'categoria_id',
            'busqueda',
            'precio_min',
            'precio_max',
            'soloOfertas',        // 👈 NUEVO: pasar a la vista
            'tituloCategoria',
            'cartCount',
            'sucursal'
        ));
    }
}