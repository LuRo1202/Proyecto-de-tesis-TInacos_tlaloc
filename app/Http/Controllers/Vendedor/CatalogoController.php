<?php
namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use App\Models\Categoria;
use App\Helpers\ProductoHelper;

class CatalogoController extends Controller  // ← ESTO DEBE SER CatalogoController
{
    public function index(Request $request)
    {
        $usuario = Auth::user();
        
        // Obtener sucursal del vendedor
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }

        // Obtener parámetros de filtro
        $categoria_id = $request->get('categoria', 0);
        $busqueda = $request->get('busqueda', '');
        $orden = $request->get('orden', 'nombre');

        // Limpiar búsqueda
        if (!empty($busqueda)) {
            $busqueda = strip_tags($busqueda);
            $busqueda = preg_replace('/[^\p{L}\p{N}\s\-\.]/u', '', $busqueda);
        }

        // Obtener categorías
        $categorias = Categoria::all();

        // Obtener productos con existencias de la sucursal
        $productosQuery = $sucursal->productos()
            ->withPivot('existencias')
            ->with(['categoria', 'color'])
            ->where('activo', 1);

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
            $tituloCategoria = "Resultados de búsqueda: '{$busqueda}'";
        } else {
            $tituloCategoria = "Todos los productos";
        }

        // Obtener productos
        $productos = $productosQuery->get();

        // Aplicar ordenamiento
        $productos = $this->ordenarProductos($productos, $orden);

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
                // El DISP-20 (Beige) es el principal
                $productosAgrupados[$familia]['principal'] = $producto;
            } 
            elseif ($familia !== 'DISP-20' && !preg_match('/-(C|N|R|AZ|RM|M|B)$/i', $codigo)) {
                // Productos sin sufijo de color son principales
                $productosAgrupados[$familia]['principal'] = $producto;
            }
        }

        // Asegurar que todas las familias tengan producto principal
        foreach ($productosAgrupados as $familia => $datos) {
            if ($datos['principal'] === null && count($datos['variantes']) > 0) {
                $productosAgrupados[$familia]['principal'] = $datos['variantes'][0];
            }
            
            // Ordenar variantes: primero las que tienen stock, luego las agotadas
            usort($productosAgrupados[$familia]['variantes'], function($a, $b) {
                $stockA = $a->pivot->existencias > 0 ? 1 : 0;
                $stockB = $b->pivot->existencias > 0 ? 1 : 0;
                return $stockB <=> $stockA;
            });
        }

        // Contar productos por categoría para los badges
        $contadores = [];
        foreach ($categorias as $categoria) {
            $contadores[$categoria->id] = $sucursal->productos()
                ->where('categoria_id', $categoria->id)
                ->wherePivot('existencias', '>', 0)
                ->where('activo', 1)
                ->count();
        }

        // Productos con bajo stock
        $bajo_stock = $sucursal->productos()
            ->wherePivot('existencias', '<=', 5)
            ->wherePivot('existencias', '>', 0)
            ->count();

        return view('vendedor.catalogo.index', compact(
            'productosAgrupados',
            'categorias',
            'categoria_id',
            'busqueda',
            'orden',
            'tituloCategoria',
            'contadores',
            'bajo_stock',
            'sucursal'
        ));
    }

    /**
     * Ordenar productos manteniendo la estructura de objetos
     */
    private function ordenarProductos($productos, $orden)
    {
        // Convertir a colección para mantener objetos
        $productos = $productos->toBase();
        
        // Ordenar según criterio
        switch ($orden) {
            case 'precio_asc':
                return $productos->sortBy('precio')->values();
            case 'precio_desc':
                return $productos->sortByDesc('precio')->values();
            case 'litros_desc':
                return $productos->sortByDesc('litros')->values();
            case 'stock':
                return $productos->sortBy(function($producto) {
                    return $producto->pivot->existencias ?? 0;
                })->values();
            case 'destacados':
                return $productos->sortByDesc('destacado')
                    ->sortBy('nombre')
                    ->values();
            default: // 'nombre'
                return $productos->sortBy('nombre')->values();
        }
    }
}