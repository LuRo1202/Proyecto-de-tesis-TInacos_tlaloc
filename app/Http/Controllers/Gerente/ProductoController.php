<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ProductoController extends Controller
{
    protected $sucursalId;
    protected $sucursalNombre;

    public function __construct()
    {
        // Verificar que el usuario es gerente
        $user = auth()->user();
        
        if (!$user || $user->rol !== 'gerente') {
            abort(403, 'Acceso no autorizado - Se requieren permisos de gerente');
        }
        
        // Obtener la sucursal del gerente desde usuario_sucursal
        $usuarioSucursal = DB::table('usuario_sucursal')
            ->where('usuario_id', $user->id)
            ->first();
        
        if (!$usuarioSucursal) {
            abort(403, 'No tienes una sucursal asignada. Contacta al administrador.');
        }
        
        $this->sucursalId = $usuarioSucursal->sucursal_id;
        
        // Obtener datos de la sucursal
        $sucursal = Sucursal::find($this->sucursalId);
        $this->sucursalNombre = $sucursal ? $sucursal->nombre : 'Sucursal no encontrada';
        
        // Guardar en sesión para el sidebar
        session([
            'sucursal_nombre' => $this->sucursalNombre,
            'sucursal_id' => $this->sucursalId
        ]);
    }

    /**
     * Lista todos los productos de la sucursal del gerente
     */
    public function index(Request $request)
    {
        // Contadores para el sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')
            ->where('sucursal_id', $this->sucursalId)
            ->count();
            
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursalId)
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        // Parámetros de filtro
        $categoria_id = $request->get('categoria');
        $busqueda = $request->get('busqueda');
        $stock_bajo = $request->has('stock_bajo');
        $pagina = $request->get('pagina', 1);
        $por_pagina = 10;
        $offset = ($pagina - 1) * $por_pagina;

        // Construir consulta base
        $query = Producto::with('categoria');

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

        // Obtener todos los productos para contar (sin paginar)
        $productosQuery = clone $query;
        $productosQuery = $productosQuery->get();

        // Agregar existencias de la sucursal
        $productosQuery = $productosQuery->map(function($producto) {
            $existencias = DB::table('producto_sucursal')
                ->where('producto_id', $producto->id)
                ->where('sucursal_id', $this->sucursalId)
                ->value('existencias') ?? 0;
            
            $producto->existencias = $existencias;
            return $producto;
        });

        // Filtrar por stock bajo si es necesario
        if ($stock_bajo) {
            $productosQuery = $productosQuery->filter(function($producto) {
                return $producto->existencias <= 5;
            })->values();
        }

        // Calcular totales
        $total_productos = $productosQuery->count();
        $total_paginas = ceil($total_productos / $por_pagina);
        
        // Paginar manualmente
        $productos = $productosQuery->slice($offset, $por_pagina)->values();

        // Estadísticas
        $productos_bajos = $productosQuery->filter(function($producto) {
            return $producto->existencias <= 5;
        })->count();

        $valor_inventario = $productosQuery->sum(function($producto) {
            return $producto->precio * $producto->existencias;
        });

        $sin_stock = $productosQuery->filter(function($producto) {
            return $producto->existencias == 0;
        })->count();

        // Obtener categorías para el filtro
        $categorias = Categoria::orderBy('nombre')->get();

        return view('gerente.productos.index', compact(
            'productos',
            'categorias',
            'total_productos',
            'total_paginas',
            'pagina',
            'productos_bajos',
            'valor_inventario',
            'sin_stock'
        ));
    }

    /**
     * Muestra el formulario para crear un nuevo producto
     */
    public function create()
    {
        // Actualizar contadores para el sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')
            ->where('sucursal_id', $this->sucursalId)
            ->count();
            
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursalId)
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        $categorias = Categoria::orderBy('nombre')->get();
        
        return view('gerente.productos.create', compact('categorias'));
    }

    /**
     * Guarda un nuevo producto en la base de datos
     */
    public function store(Request $request)
    {
        Log::info('=== INICIO CREACIÓN DE PRODUCTO (GERENTE) ===');
        
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'nombre' => 'required|string|max:200',
            'litros' => 'nullable|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio' => 'required|numeric|min:0.01',
            'existencias' => 'nullable|integer|min:0',
            'activo' => 'nullable',
            'destacado' => 'nullable',
            'imagen' => 'nullable|file|mimes:jpg,jpeg|max:2048'
        ]);

        // Determinar valores booleanos
        $activo = $request->has('activo');
        $destacado = $request->has('destacado');
        $litros = $validated['litros'] ?? 0;
        $existencias = $validated['existencias'] ?? 0;

        try {
            DB::beginTransaction();

            // Crear producto
            $producto = Producto::create([
                'codigo' => $validated['codigo'],
                'nombre' => $validated['nombre'],
                'litros' => $litros,
                'categoria_id' => $validated['categoria_id'] ?? null,
                'precio' => $validated['precio'],
                'activo' => $activo,
                'destacado' => $destacado
            ]);

            // Insertar en producto_sucursal para la sucursal del gerente
            DB::table('producto_sucursal')->insert([
                'producto_id' => $producto->id,
                'sucursal_id' => $this->sucursalId,
                'existencias' => $existencias,
                'stock_minimo' => 0,
                'stock_maximo' => 0,
                'fecha_actualizacion' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Procesar imagen si se subió
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                
                // Crear directorio si no existe
                $path = public_path('assets/img/productos');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }

                // Guardar imagen
                $nombre_archivo = $producto->codigo . '.jpg';
                $imagen->move($path, $nombre_archivo);
                
                Log::info('Imagen guardada: ' . $nombre_archivo);
            }

            DB::commit();

            return redirect()->route('gerente.productos')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Producto creado!',
                    'message' => "El producto {$producto->nombre} ha sido creado correctamente en tu sucursal."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear producto (gerente): ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al crear el producto: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Muestra el formulario para editar un producto
     */
    public function edit($id)
    {
        // Obtener producto
        $producto = Producto::with('categoria')->findOrFail($id);
        
        // Obtener existencias en la sucursal del gerente
        $existencias = DB::table('producto_sucursal')
            ->where('producto_id', $id)
            ->where('sucursal_id', $this->sucursalId)
            ->value('existencias') ?? 0;
        
        $producto->existencias = $existencias;

        // Verificar si existe imagen
        $imagen_path = public_path('assets/img/productos/' . $producto->codigo . '.jpg');
        $imagen_existe = file_exists($imagen_path);

        // Obtener categorías
        $categorias = Categoria::orderBy('nombre')->get();

        // Actualizar contadores para el sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')
            ->where('sucursal_id', $this->sucursalId)
            ->count();
            
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('sucursal_id', $this->sucursalId)
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        return view('gerente.productos.edit', compact('producto', 'categorias', 'imagen_existe'));
    }

    /**
     * Actualiza un producto existente
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo,' . $id,
            'nombre' => 'required|string|max:200',
            'litros' => 'nullable|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio' => 'required|numeric|min:0.01',
            'existencias' => 'nullable|integer|min:0',
            'activo' => 'nullable',
            'destacado' => 'nullable',
            'imagen' => 'nullable|file|mimes:jpg,jpeg|max:2048',
            'eliminar_imagen' => 'sometimes|in:0,1'
        ]);

        // Determinar valores booleanos
        $activo = $request->has('activo');
        $destacado = $request->has('destacado');
        $litros = $validated['litros'] ?? 0;
        $existencias = $validated['existencias'] ?? 0;

        try {
            DB::beginTransaction();

            // Guardar código anterior para manejo de imágenes
            $codigo_anterior = $producto->codigo;

            // Actualizar producto
            $producto->update([
                'codigo' => $validated['codigo'],
                'nombre' => $validated['nombre'],
                'litros' => $litros,
                'categoria_id' => $validated['categoria_id'] ?? null,
                'precio' => $validated['precio'],
                'activo' => $activo,
                'destacado' => $destacado
            ]);

            // Actualizar existencias en producto_sucursal
            DB::table('producto_sucursal')
                ->updateOrInsert(
                    [
                        'producto_id' => $id,
                        'sucursal_id' => $this->sucursalId
                    ],
                    [
                        'existencias' => $existencias,
                        'updated_at' => now()
                    ]
                );

            // Manejar eliminación de imagen si se solicitó
            if ($request->input('eliminar_imagen') == 1) {
                $ruta_imagen = public_path('assets/img/productos/' . $codigo_anterior . '.jpg');
                if (file_exists($ruta_imagen)) {
                    unlink($ruta_imagen);
                }
            }

            // Procesar nueva imagen si se subió
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');

                // Crear directorio si no existe
                $path = public_path('assets/img/productos');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }

                // Eliminar imagen anterior si existe (con código anterior o nuevo)
                $ruta_imagen_anterior = public_path('assets/img/productos/' . $codigo_anterior . '.jpg');
                if (file_exists($ruta_imagen_anterior)) {
                    unlink($ruta_imagen_anterior);
                }

                $ruta_imagen_nueva = public_path('assets/img/productos/' . $producto->codigo . '.jpg');
                if (file_exists($ruta_imagen_nueva)) {
                    unlink($ruta_imagen_nueva);
                }

                // Guardar nueva imagen
                $nombre_archivo = $producto->codigo . '.jpg';
                $imagen->move($path, $nombre_archivo);
            } elseif ($codigo_anterior != $producto->codigo) {
                // Si cambió el código pero no se subió nueva imagen, renombrar la imagen existente
                $ruta_imagen_anterior = public_path('assets/img/productos/' . $codigo_anterior . '.jpg');
                $ruta_imagen_nueva = public_path('assets/img/productos/' . $producto->codigo . '.jpg');
                
                if (file_exists($ruta_imagen_anterior)) {
                    rename($ruta_imagen_anterior, $ruta_imagen_nueva);
                }
            }

            DB::commit();

            return redirect()->route('gerente.productos')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Producto actualizado!',
                    'message' => "El producto {$producto->nombre} ha sido actualizado correctamente."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar producto: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('swal', [
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

            // Eliminar de producto_sucursal
            DB::table('producto_sucursal')
                ->where('producto_id', $id)
                ->where('sucursal_id', $this->sucursalId)
                ->delete();

            // Verificar si es el único registro en producto_sucursal
            $existenRegistros = DB::table('producto_sucursal')
                ->where('producto_id', $id)
                ->count();

            // Si no hay más registros, eliminar el producto
            if ($existenRegistros == 0) {
                // Eliminar imagen si existe
                $ruta_imagen = public_path('assets/img/productos/' . $producto->codigo . '.jpg');
                if (file_exists($ruta_imagen)) {
                    unlink($ruta_imagen);
                }

                // Eliminar producto
                $producto->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "El producto {$nombre} ha sido eliminado correctamente de tu sucursal."
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