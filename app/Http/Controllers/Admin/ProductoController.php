<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\Sucursal;
use App\Helpers\SucursalHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ProductoController extends Controller
{
    /**
     * Lista todos los productos con filtros (por defecto muestra stock total de todas las sucursales)
     */
    public function index(Request $request)
    {
        // Obtener parámetros de filtro
        $sucursal_id = $request->get('sucursal_id');
        $categoria_id = $request->get('categoria');
        $busqueda = $request->get('busqueda');
        $stock_bajo = $request->has('stock_bajo');
        $pagina = $request->get('pagina', 1);
        $por_pagina = 20;
        $offset = ($pagina - 1) * $por_pagina;

        // Contadores para el sidebar (sin filtrar por sucursal para los badges)
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')->count();
        
        // Calcular productos con stock bajo (TOTAL de todas las sucursales para el badge)
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        // Guardar en sesión para el sidebar
        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        // Construir consulta base
        $query = Producto::with('categoria')
            ->with(['sucursales' => function($q) use ($sucursal_id) {
                if ($sucursal_id) {
                    $q->where('sucursal_id', $sucursal_id);
                }
            }]);

        // Aplicar filtros
        if ($categoria_id) {
            $query->where('categoria_id', $categoria_id);
        }

        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('codigo', 'LIKE', "%{$busqueda}%")
                  ->orWhere('nombre', 'LIKE', "%{$busqueda}%");
            });
        }

        // Obtener todos los productos
        $productos = $query->orderBy('codigo')->get();

        // Calcular existencias (TOTALES o por sucursal según filtro)
        $productos = $productos->map(function($producto) use ($sucursal_id) {
            if ($sucursal_id) {
                // Si hay sucursal seleccionada, mostrar stock de esa sucursal
                $producto->existencias = $producto->sucursales->first()->pivot->existencias ?? 0;
            } else {
                // Si NO hay sucursal seleccionada, sumar stock de TODAS las sucursales
                $producto->existencias = $producto->sucursales->sum(function($sucursal) {
                    return $sucursal->pivot->existencias ?? 0;
                });
            }
            return $producto;
        });

        // Filtrar por stock bajo si es necesario
        if ($stock_bajo) {
            $productos = $productos->filter(function($producto) {
                return $producto->existencias <= 5;
            })->values();
        }

        // Calcular total y paginar manualmente
        $total_productos = $productos->count();
        $total_paginas = ceil($total_productos / $por_pagina);
        $productos_paginados = $productos->slice($offset, $por_pagina)->values();

        // Calcular valor total del inventario (con stock TOTAL)
        $valor_inventario = $productos->sum(function($producto) {
            return $producto->precio * $producto->existencias;
        });

        // Calcular productos sin stock
        $sin_stock = $productos->filter(function($producto) {
            return $producto->existencias == 0;
        })->count();

        // Productos con stock bajo (para el filtro)
        $productos_bajos = $productos->filter(function($producto) {
            return $producto->existencias <= 5 && $producto->existencias > 0;
        })->count();

        // Obtener todas las categorías para el filtro
        $categorias = Categoria::orderBy('nombre')->get();

        // Obtener todas las sucursales activas para el filtro
        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();

        return view('admin.productos.index', compact(
            'productos_paginados',
            'categorias',
            'sucursales',
            'total_productos',
            'total_paginas',
            'pagina',
            'productos_bajos',
            'valor_inventario',
            'sin_stock',
            'sucursal_id'
        ));
    }

    /**
     * Muestra el formulario para crear un nuevo producto
     */
    public function create(Request $request)
    {
        // Obtener la sucursal actual o seleccionada
        $sucursal_id = $request->get('sucursal_id');
        
        if (!$sucursal_id) {
            $sucursal_actual = SucursalHelper::getSucursalActual();
            $sucursal_id = $sucursal_actual ? $sucursal_actual->id : 1;
        }
        
        // Obtener contadores para badges del sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')->count();
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        // Guardar en sesión para el sidebar
        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        $categorias = Categoria::orderBy('nombre')->get();
        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();
        
        $sucursal_seleccionada = $sucursal_id;
        
        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->map(function($producto) use ($sucursal_id) {
                $existencias = DB::table('producto_sucursal')
                    ->where('producto_id', $producto->id)
                    ->where('sucursal_id', $sucursal_id)
                    ->value('existencias') ?? 0;
                
                $producto->existencias = $existencias;
                return $producto;
            });
        
        return view('admin.productos.create', compact(
            'categorias', 
            'sucursales', 
            'sucursal_id',
            'sucursal_seleccionada',
            'productos'
        ));
    }

    /**
     * Guarda un nuevo producto en la base de datos
     */
    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'nombre' => 'required|string|max:200',
            'litros' => 'nullable|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio' => 'required|numeric|min:0.01',
            'existencias' => 'nullable|integer|min:0',
            'sucursal_id' => 'required|exists:sucursales,id',
            'activo' => 'nullable',
            'destacado' => 'nullable',
            'imagen' => 'nullable|file|mimes:jpg,jpeg|max:2048'
        ]);

        $activo = $request->has('activo');
        $destacado = $request->has('destacado');
        $litros = $validated['litros'] ?? 0;
        $existencias = $validated['existencias'] ?? 0;

        try {
            DB::beginTransaction();

            $producto = Producto::create([
                'codigo' => $validated['codigo'],
                'nombre' => $validated['nombre'],
                'litros' => $litros,
                'categoria_id' => $validated['categoria_id'] ?? null,
                'precio' => $validated['precio'],
                'activo' => $activo,
                'destacado' => $destacado
            ]);

            DB::table('producto_sucursal')->insert([
                'producto_id' => $producto->id,
                'sucursal_id' => $validated['sucursal_id'],
                'existencias' => $existencias,
                'stock_minimo' => 0,
                'stock_maximo' => 0,
                'fecha_actualizacion' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $path = public_path('assets/img/productos');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }
                $nombre_archivo = $producto->codigo . '.jpg';
                $imagen->move($path, $nombre_archivo);
            }

            DB::commit();

            // Redirigir a la lista de productos sin aplicar filtros automáticos
            return redirect()->route('admin.productos')
                ->with('swal_producto', [
                    'type' => 'success',
                    'title' => '¡Producto creado!',
                    'message' => "El producto {$producto->nombre} ha sido creado correctamente."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear producto: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('swal_producto', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al crear el producto: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Muestra el formulario para editar un producto
     */
    public function edit(Request $request, $id)
    {
        $sucursal_id = $request->get('sucursal_id');
        
        if (!$sucursal_id) {
            $sucursal_actual = SucursalHelper::getSucursalActual();
            $sucursal_id = $sucursal_actual ? $sucursal_actual->id : 1;
        }
        
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')->count();
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        $producto = Producto::with('categoria')->findOrFail($id);
        
        $existencias = DB::table('producto_sucursal')
            ->where('producto_id', $id)
            ->where('sucursal_id', $sucursal_id)
            ->value('existencias') ?? 0;
        
        $categorias = Categoria::orderBy('nombre')->get();
        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();
        
        $queryParams = $request->only(['sucursal_id', 'categoria', 'busqueda', 'stock_bajo', 'pagina']);
        
        return view('admin.productos.edit', compact(
            'producto', 
            'categorias', 
            'sucursales',
            'existencias', 
            'sucursal_id',
            'queryParams'
        ));
    }

    /**
     * Actualiza un producto existente
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        
        $queryParams = [];
        if ($request->has('sucursal_id') && !empty($request->sucursal_id)) {
            $queryParams['sucursal_id'] = $request->sucursal_id;
        }
        if ($request->has('categoria') && !empty($request->categoria)) {
            $queryParams['categoria'] = $request->categoria;
        }
        if ($request->has('busqueda') && !empty($request->busqueda)) {
            $queryParams['busqueda'] = $request->busqueda;
        }
        if ($request->has('stock_bajo') && $request->stock_bajo == '1') {
            $queryParams['stock_bajo'] = 1;
        }
        if ($request->has('pagina') && $request->pagina > 1) {
            $queryParams['pagina'] = $request->pagina;
        }
        
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo,' . $id,
            'nombre' => 'required|string|max:200',
            'litros' => 'nullable|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio' => 'required|numeric|min:0.01',
            'existencias' => 'nullable|integer|min:0',
            'sucursal_id' => 'required|exists:sucursales,id',
            'activo' => 'nullable',
            'destacado' => 'nullable',
            'imagen' => 'nullable|file|mimes:jpg,jpeg|max:2048',
            'eliminar_imagen' => 'sometimes|in:0,1'
        ]);

        $activo = $request->has('activo');
        $destacado = $request->has('destacado');
        $litros = $validated['litros'] ?? 0;
        $existencias = $validated['existencias'] ?? 0;

        try {
            DB::beginTransaction();

            $codigo_anterior = $producto->codigo;

            $producto->update([
                'codigo' => $validated['codigo'],
                'nombre' => $validated['nombre'],
                'litros' => $litros,
                'categoria_id' => $validated['categoria_id'] ?? null,
                'precio' => $validated['precio'],
                'activo' => $activo,
                'destacado' => $destacado
            ]);

            $existe_registro = DB::table('producto_sucursal')
                ->where('producto_id', $id)
                ->where('sucursal_id', $validated['sucursal_id'])
                ->exists();

            if ($existe_registro) {
                DB::table('producto_sucursal')
                    ->where('producto_id', $id)
                    ->where('sucursal_id', $validated['sucursal_id'])
                    ->update([
                        'existencias' => $existencias,
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('producto_sucursal')->insert([
                    'producto_id' => $id,
                    'sucursal_id' => $validated['sucursal_id'],
                    'existencias' => $existencias,
                    'stock_minimo' => 0,
                    'stock_maximo' => 0,
                    'fecha_actualizacion' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            if ($request->input('eliminar_imagen') == 1) {
                $ruta_imagen = public_path('assets/img/productos/' . $codigo_anterior . '.jpg');
                if (File::exists($ruta_imagen)) {
                    File::delete($ruta_imagen);
                }
            }

            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $path = public_path('assets/img/productos');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }

                $ruta_imagen_anterior = public_path('assets/img/productos/' . $codigo_anterior . '.jpg');
                if (File::exists($ruta_imagen_anterior)) {
                    File::delete($ruta_imagen_anterior);
                }

                $ruta_imagen_nueva = public_path('assets/img/productos/' . $producto->codigo . '.jpg');
                if (File::exists($ruta_imagen_nueva)) {
                    File::delete($ruta_imagen_nueva);
                }

                $nombre_archivo = $producto->codigo . '.jpg';
                $imagen->move($path, $nombre_archivo);
            } elseif ($codigo_anterior != $producto->codigo) {
                $ruta_imagen_anterior = public_path('assets/img/productos/' . $codigo_anterior . '.jpg');
                $ruta_imagen_nueva = public_path('assets/img/productos/' . $producto->codigo . '.jpg');
                
                if (File::exists($ruta_imagen_anterior)) {
                    File::move($ruta_imagen_anterior, $ruta_imagen_nueva);
                }
            }

            DB::commit();

            // Redirigir a la lista de productos sin aplicar filtros automáticos
            return redirect()->route('admin.productos')
                ->with('swal_producto', [
                    'type' => 'success',
                    'title' => '¡Producto actualizado!',
                    'message' => "El producto {$producto->nombre} ha sido actualizado correctamente."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar producto: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('swal_producto', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al actualizar el producto: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Elimina un producto (API AJAX)
     */
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $nombre = $producto->nombre;
            
            DB::beginTransaction();

            DB::table('producto_sucursal')->where('producto_id', $id)->delete();

            $ruta_imagen = public_path('assets/img/productos/' . $producto->codigo . '.jpg');
            if (File::exists($ruta_imagen)) {
                File::delete($ruta_imagen);
            }

            $producto->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "El producto {$nombre} ha sido eliminado correctamente."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar producto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'No se pudo eliminar el producto. Error: ' . $e->getMessage()
            ], 500);
        }
    }
}