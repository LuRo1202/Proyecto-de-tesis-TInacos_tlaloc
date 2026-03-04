<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PedidoHistorial;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PedidoController extends Controller
{
    /**
     * Lista todos los pedidos con filtros
     */
    public function index(Request $request)
    {
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')->count();
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();

        $estado = $request->get('estado');
        $sucursal_id = $request->get('sucursal_id');
        $fecha = $request->get('fecha');

        $query = Pedido::with(['items', 'sucursal'])
            ->select('pedidos.*');

        if ($estado) {
            $query->where('pedidos.estado', $estado);
        }

        if ($sucursal_id) {
            $query->where('pedidos.sucursal_id', $sucursal_id);
        }

        if ($fecha) {
            $query->whereDate('pedidos.fecha', $fecha);
        }

        $pedidos = $query->orderBy('pedidos.fecha', 'desc')->get()
            ->map(function($pedido) {
                $pedido->fecha = Carbon::parse($pedido->fecha);
                $pedido->items_count = $pedido->items->count();
                $pedido->total_items = $pedido->items->sum('cantidad');
                return $pedido;
            });

        $estados_count = Pedido::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        $total_general = $pedidos->sum('total');

        return view('admin.pedidos.index', compact(
            'pedidos',
            'estados_count',
            'total_general',
            'estado',
            'sucursal_id',      
            'sucursales',       
            'fecha'
        ));
    }

    /**
     * Muestra los detalles de un pedido específico
     */
    public function show($id)
    {
        $pedido = Pedido::with(['items', 'sucursal'])->findOrFail($id);
        $pedido->fecha = Carbon::parse($pedido->fecha);
        
        $items = $pedido->items;
        foreach ($items as $item) {
            $producto = Producto::find($item->producto_id);
            if ($producto) {
                $item->codigo = $producto->codigo;
                $item->litros = $producto->litros;
            }
        }
        
        $pedido->total_items = $items->sum('cantidad');
        
        $responsable = DB::table('pedido_responsables')
            ->join('usuarios', 'pedido_responsables.usuario_id', '=', 'usuarios.id')
            ->where('pedido_responsables.pedido_id', $id)
            ->select('usuarios.id', 'usuarios.nombre', 'usuarios.usuario', 'usuarios.rol')
            ->first();
        
        $usuarios_sucursal = collect();
        if ($pedido->sucursal_id) {
            $usuarios_sucursal = Usuario::whereIn('rol', ['vendedor', 'gerente'])
                ->where('activo', true)
                ->whereHas('sucursales', function($q) use ($pedido) {
                    $q->where('sucursal_id', $pedido->sucursal_id);
                })
                ->orderBy('rol', 'desc')
                ->orderBy('nombre')
                ->get();
        }
        
        $historial = PedidoHistorial::with('usuario')
            ->where('pedido_id', $id)
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function($item) {
                $item->usuario_nombre = $item->usuario->nombre ?? 'Sistema';
                $item->usuario_rol = $item->usuario->rol ?? 'sistema';
                return $item;
            });
        
        return view('admin.pedidos.show', compact('pedido', 'items', 'responsable', 'usuarios_sucursal', 'historial'));
    }

    /**
     * Muestra el formulario para editar un pedido
     */
    public function edit($id)
    {
        $pedido = Pedido::with('sucursal')->findOrFail($id);
        $pedido->fecha = Carbon::parse($pedido->fecha);
        
        $sucursales = Sucursal::where('activa', true)
            ->orderBy('nombre')
            ->get();
        
        $responsable_actual = DB::table('pedido_responsables')
            ->join('usuarios', 'pedido_responsables.usuario_id', '=', 'usuarios.id')
            ->where('pedido_responsables.pedido_id', $id)
            ->select('usuarios.id', 'usuarios.nombre', 'usuarios.usuario', 'usuarios.rol', 'usuarios.email')
            ->first();
        
        $usuarios_sucursal = collect();
        if ($pedido->sucursal_id) {
            $usuarios_sucursal = Usuario::whereIn('rol', ['vendedor', 'gerente'])
                ->where('activo', true)
                ->whereHas('sucursales', function($q) use ($pedido) {
                    $q->where('sucursal_id', $pedido->sucursal_id);
                })
                ->orderBy('rol', 'desc')
                ->orderBy('nombre')
                ->get();
        }
        
        return view('admin.pedidos.edit', compact(
            'pedido', 
            'sucursales', 
            'responsable_actual', 
            'usuarios_sucursal'
        ));
    }

    /**
     * Actualiza un pedido existente
     */
    public function update(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        
        $request->validate([
            'estado' => 'required|in:pendiente,confirmado,enviado,entregado,cancelado',
            'notas' => 'nullable|string',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'fecha_entrega' => 'nullable|date',
            'distancia_km' => 'nullable|numeric|min:0',
            'responsable_id' => 'nullable|exists:usuarios,id'
        ]);

        $fecha_confirmacion = $pedido->fecha_confirmacion;
        if ($request->estado == 'confirmado' && !$pedido->fecha_confirmacion) {
            $fecha_confirmacion = now();
        } elseif ($request->estado != 'confirmado' && $pedido->estado == 'confirmado') {
            $fecha_confirmacion = null;
        }

        // ✅ Si se está cancelando el pedido, regresar el stock
        if ($request->estado == 'cancelado' && $pedido->estado != 'cancelado') {
            $this->regresarStock($pedido);
        }

        // ✅ Si se estaba cancelado y ahora se cambia a otro estado, quitar el stock
        if ($pedido->estado == 'cancelado' && $request->estado != 'cancelado') {
            $this->descontarStock($pedido);
        }

        $pedido->update([
            'estado' => $request->estado,
            'pago_confirmado' => $request->has('pago_confirmado'),
            'sucursal_id' => $request->sucursal_id,
            'fecha_entrega' => $request->fecha_entrega,
            'notas' => $request->notas,
            'distancia_km' => $request->distancia_km,
            'cobertura_verificada' => $request->has('cobertura_verificada'),
            'fecha_confirmacion' => $fecha_confirmacion
        ]);

        $usuario_id = auth()->check() ? auth()->id() : 1;
        if (!is_numeric($usuario_id)) {
            $usuario_id = 1;
        }

        if ($request->filled('responsable_id')) {
            $usuario = Usuario::find($request->responsable_id);
            if ($usuario && $request->sucursal_id) {
                if ($usuario->sucursales()->where('sucursal_id', $request->sucursal_id)->exists()) {
                    DB::table('pedido_responsables')->updateOrInsert(
                        ['pedido_id' => $id],
                        [
                            'usuario_id' => $request->responsable_id,
                            'fecha_asignacion' => now()
                        ]
                    );
                    
                    PedidoHistorial::create([
                        'pedido_id' => $id,
                        'usuario_id' => (int)$usuario_id,
                        'accion' => 'responsable_asignado',
                        'detalles' => "Responsable asignado: {$usuario->nombre}",
                        'fecha' => now()
                    ]);
                }
            }
        } else {
            DB::table('pedido_responsables')->where('pedido_id', $id)->delete();
        }

        PedidoHistorial::create([
            'pedido_id' => $id,
            'usuario_id' => (int)$usuario_id,
            'accion' => 'actualizado',
            'detalles' => 'Pedido editado',
            'fecha' => now()
        ]);

        // ✅ CORREGIDO: Cambiado de 'swal' a 'swal_pedido'
        return redirect()->route('admin.pedidos.ver', $id)
            ->with('swal_pedido', [
                'type' => 'success',
                'title' => '¡Pedido actualizado!',
                'message' => 'El pedido ha sido actualizado correctamente.'
            ]);
    }

    /**
     * Elimina un pedido - ✅ REGRESA STOCK
     */
    public function destroy($id)
    {
        $pedido = Pedido::with('items')->findOrFail($id);
        $folio = $pedido->folio;
        
        try {
            DB::beginTransaction();
            
            // ✅ REGRESAR STOCK ANTES DE ELIMINAR
            if ($pedido->estado != 'cancelado') {
                $this->regresarStock($pedido);
            }
            
            // Eliminar items relacionados
            PedidoItem::where('pedido_id', $id)->delete();
            
            // Eliminar historial
            PedidoHistorial::where('pedido_id', $id)->delete();
            
            // Eliminar responsables
            DB::table('pedido_responsables')->where('pedido_id', $id)->delete();
            
            // Eliminar pedido
            $pedido->delete();
            
            DB::commit();
            
            // ✅ CORREGIDO: Cambiado de 'swal' a 'swal_pedido'
            return redirect()->route('admin.pedidos')
                ->with('swal_pedido', [
                    'type' => 'success',
                    'title' => 'Pedido eliminado',
                    'message' => "El pedido #{$folio} ha sido eliminado correctamente."
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar pedido: ' . $e->getMessage());
            
            // ✅ CORREGIDO: Cambiado de 'swal' a 'swal_pedido'
            return redirect()->route('admin.pedidos')
                ->with('swal_pedido', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'No se pudo eliminar el pedido.'
                ]);
        }
    }

    /**
     * Procesa acciones sobre el pedido - ✅ REGRESA STOCK AL CANCELAR
     */
    public function procesar(Request $request, $accion, $id)
    {
        $pedido = Pedido::with('items')->findOrFail($id);
        
        $acciones_permitidas = [
            'confirmar' => 'confirmado',
            'enviar' => 'enviado',
            'entregar' => 'entregado',
            'cancelar' => 'cancelado',
            'confirmar_pago' => null
        ];
        
        if (!array_key_exists($accion, $acciones_permitidas)) {
            return redirect()->route('admin.pedidos.ver', $id)
                ->with('swal_pedido', [  // ✅ CORREGIDO
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Acción no válida'
                ]);
        }
        
        try {
            DB::beginTransaction();
            
            $usuario_id = auth()->check() ? auth()->id() : 1;
            if (!is_numeric($usuario_id)) {
                $usuario_id = 1;
            }
            
            if ($accion == 'confirmar_pago') {
                $pedido->pago_confirmado = true;
                $pedido->fecha_confirmacion = now();
                $pedido->save();
                
                PedidoHistorial::create([
                    'pedido_id' => $pedido->id,
                    'usuario_id' => (int)$usuario_id,
                    'accion' => 'pago_confirmado',
                    'detalles' => 'Pago confirmado',
                    'fecha' => now()
                ]);
                
                $mensaje = 'Pago confirmado correctamente';
            } else {
                $nuevo_estado = $acciones_permitidas[$accion];
                
                // ✅ SI SE CANCELA, REGRESAR STOCK
                if ($accion == 'cancelar' && $pedido->estado != 'cancelado') {
                    $this->regresarStock($pedido);
                }
                
                $pedido->estado = $nuevo_estado;
                
                if ($accion == 'entregar') {
                    $pedido->fecha_entrega = now();
                }
                
                $pedido->save();
                
                PedidoHistorial::create([
                    'pedido_id' => $pedido->id,
                    'usuario_id' => (int)$usuario_id,
                    'accion' => $accion,
                    'detalles' => "Estado cambiado a: {$nuevo_estado}",
                    'fecha' => now()
                ]);
                
                $mensaje = "Pedido {$nuevo_estado} correctamente";
            }
            
            DB::commit();
            
            // ✅ CORREGIDO: Cambiado de 'swal' a 'swal_pedido'
            return redirect()->route('admin.pedidos.ver', $id)
                ->with('swal_pedido', [
                    'type' => 'success',
                    'title' => '¡Procesado!',
                    'message' => $mensaje
                ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar pedido: ' . $e->getMessage());
            
            // ✅ CORREGIDO: Cambiado de 'swal' a 'swal_pedido'
            return redirect()->route('admin.pedidos.ver', $id)
                ->with('swal_pedido', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'No se pudo procesar la acción'
                ]);
        }
    }

    /**
     * ✅ FUNCIÓN AUXILIAR: Regresar stock al inventario
     */
    private function regresarStock($pedido)
    {
        foreach ($pedido->items as $item) {
            DB::table('producto_sucursal')
                ->where('producto_id', $item->producto_id)
                ->where('sucursal_id', $pedido->sucursal_id)
                ->increment('existencias', $item->cantidad);
        }
    }

    /**
     * ✅ FUNCIÓN AUXILIAR: Descontar stock del inventario
     */
    private function descontarStock($pedido)
    {
        foreach ($pedido->items as $item) {
            DB::table('producto_sucursal')
                ->where('producto_id', $item->producto_id)
                ->where('sucursal_id', $pedido->sucursal_id)
                ->decrement('existencias', $item->cantidad);
        }
    }

    /**
     * Asigna un responsable al pedido
     */
    public function asignarResponsable(Request $request)
    {
        $pedido_id = $request->get('pedido_id');
        $usuario_id = $request->get('usuario_id');
        
        if (!$pedido_id || !$usuario_id) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }
        
        try {
            $auth_usuario_id = auth()->check() ? auth()->id() : 1;
            if (!is_numeric($auth_usuario_id)) {
                $auth_usuario_id = 1;
            }
            
            DB::table('pedido_responsables')->updateOrInsert(
                ['pedido_id' => $pedido_id],
                [
                    'usuario_id' => $usuario_id,
                    'fecha_asignacion' => now()
                ]
            );
            
            $usuario = Usuario::find($usuario_id);
            
            PedidoHistorial::create([
                'pedido_id' => $pedido_id,
                'usuario_id' => (int)$auth_usuario_id,
                'accion' => 'responsable_asignado',
                'detalles' => "Responsable asignado: {$usuario->nombre}",
                'fecha' => now()
            ]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error al asignar responsable: ' . $e->getMessage());
            return response()->json(['error' => 'Error al asignar responsable'], 500);
        }
    }

    /**
     * Remueve el responsable del pedido
     */
    public function removerResponsable(Request $request)
    {
        $pedido_id = $request->get('pedido_id');
        
        if (!$pedido_id) {
            return response()->json(['error' => 'Faltan parámetros'], 400);
        }
        
        try {
            $auth_usuario_id = auth()->check() ? auth()->id() : 1;
            if (!is_numeric($auth_usuario_id)) {
                $auth_usuario_id = 1;
            }
            
            DB::table('pedido_responsables')
                ->where('pedido_id', $pedido_id)
                ->delete();
            
            PedidoHistorial::create([
                'pedido_id' => $pedido_id,
                'usuario_id' => (int)$auth_usuario_id,
                'accion' => 'responsable_removido',
                'detalles' => 'Responsable removido del pedido',
                'fecha' => now()
            ]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error al remover responsable: ' . $e->getMessage());
            return response()->json(['error' => 'Error al remover responsable'], 500);
        }
    }
}