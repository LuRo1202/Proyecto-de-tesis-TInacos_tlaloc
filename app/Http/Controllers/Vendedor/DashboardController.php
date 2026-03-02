<?php
// app/Http/Controllers/Vendedor/DashboardController.php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\PedidoResponsable;
use App\Models\PedidoItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        
        // Obtener la sucursal del vendedor
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('login')
                ->with('error', 'No tienes una sucursal asignada.');
        }
        
        $sucursalNombre = $sucursal->nombre;
        $sucursal_id = $sucursal->id;

        // Estadísticas del vendedor
        $stats = $this->getEstadisticas($usuario_id, $sucursal_id);
        
        // Mis pedidos asignados
        $misPedidos = $this->getMisPedidos($usuario_id, $sucursal_id);
        
        // Pedidos disponibles en sucursal (NO asignados)
        $pedidosDisponibles = $this->getPedidosDisponibles($sucursal_id);
        
        // Mis productos más vendidos
        $productosVendidos = $this->getProductosMasVendidos($usuario_id, $sucursal_id);

        return view('vendedor.dashboard.index', compact(
            'stats',
            'misPedidos',
            'pedidosDisponibles',
            'productosVendidos',
            'sucursal',
            'sucursalNombre',
            'usuario'
        ));
    }

    public function asignarPedido(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|integer|exists:pedidos,id'
        ]);

        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes una sucursal asignada.'
            ], 403);
        }
        
        $sucursal_id = $sucursal->id;
        $pedido_id = $request->pedido_id;

        try {
            DB::beginTransaction();

            // Verificar que el pedido exista y pertenezca a la sucursal
            $pedido = Pedido::where('id', $pedido_id)
                ->where('sucursal_id', $sucursal_id)
                ->whereIn('estado', ['pendiente', 'confirmado'])
                ->first();

            if (!$pedido) {
                return response()->json([
                    'success' => false,
                    'message' => 'El pedido no está disponible en tu sucursal o ya fue procesado'
                ], 404);
            }

            // Verificar si ya tiene vendedores asignados
            $vendedoresAsignados = PedidoResponsable::where('pedido_id', $pedido_id)->count();
            
            if ($vendedoresAsignados > 0) {
                $nombres = PedidoResponsable::where('pedido_id', $pedido_id)
                    ->with('usuario')
                    ->get()
                    ->pluck('usuario.nombre')
                    ->implode(', ');
                    
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido ya está asignado a: ' . $nombres
                ], 400);
            }

            // Verificar si el usuario ya está asignado
            $yaAsignado = PedidoResponsable::where('pedido_id', $pedido_id)
                ->where('usuario_id', $usuario_id)
                ->exists();

            if ($yaAsignado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya estás asignado a este pedido'
                ], 400);
            }

            // Asignar vendedor al pedido
            PedidoResponsable::create([
                'pedido_id' => $pedido_id,
                'usuario_id' => $usuario_id,
                'fecha_asignacion' => now()
            ]);

            // Registrar en historial
            DB::table('pedido_historial')->insert([
                'pedido_id' => $pedido_id,
                'usuario_id' => $usuario_id,
                'accion' => 'responsable_asignado',
                'detalles' => "Vendedor {$usuario->nombre} asignado al pedido",
                'fecha' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Pedido asignado correctamente! Ahora eres el responsable.',
                'folio' => $pedido->folio
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getEstadisticas($usuario_id, $sucursal_id)
    {
        $stats = [];

        // 1. Pedidos asignados al vendedor (todos)
        $stats['pedidos_asignados'] = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->count();

        // 2. Pedidos pendientes asignados
        $stats['pedidos_pendientes'] = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->where('estado', 'pendiente')
            ->count();

        // 3. Pedidos disponibles (NO asignados)
        $stats['pedidos_disponibles'] = Pedido::where('sucursal_id', $sucursal_id)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->whereDoesntHave('responsables')
            ->count();

        // 4. Ventas del mes
        $stats['ventas_mes'] = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->where('pago_confirmado', 1)
            ->where('estado', 'entregado')
            ->sum('total') ?: 0;

        // 5. Ventas del día
        $stats['ventas_hoy'] = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->whereDate('fecha', Carbon::today())
            ->where('pago_confirmado', 1)
            ->where('estado', 'entregado')
            ->sum('total') ?: 0;

        // 6. Clientes atendidos
        $stats['clientes_atendidos'] = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->distinct('cliente_telefono')
            ->count('cliente_telefono');

        // 7. Comisiones estimadas (5%)
        $stats['comisiones_mes'] = $stats['ventas_mes'] * 0.05;

        return $stats;
    }

    private function getMisPedidos($usuario_id, $sucursal_id, $limit = 8)
    {
        return Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->withCount('items as items_count')
            ->orderBy('fecha', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getPedidosDisponibles($sucursal_id, $limit = 6)
    {
        return Pedido::where('sucursal_id', $sucursal_id)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->whereDoesntHave('responsables')
            ->withCount('items as items_count')
            ->orderBy('fecha', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getProductosMasVendidos($usuario_id, $sucursal_id, $limit = 5)
    {
        return PedidoItem::join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->join('pedido_responsables', 'pedidos.id', '=', 'pedido_responsables.pedido_id')
            ->where('pedido_responsables.usuario_id', $usuario_id)
            ->where('pedidos.sucursal_id', $sucursal_id)
            ->where('pedidos.estado', 'entregado')
            ->select(
                'pedido_items.producto_nombre',
                DB::raw('SUM(pedido_items.cantidad) as total_vendido')
            )
            ->groupBy('pedido_items.producto_nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Actualizar contadores para AJAX (tiempo real)
     */
    public function actualizarContadores()
    {
        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return response()->json([
                'pedidos_asignados' => 0,
                'pedidos_urgentes' => 0,
                'pedidos_disponibles' => 0,
                'ventas_mes' => 0
            ]);
        }
        
        $sucursal_id = $sucursal->id;
        
        // Pedidos asignados a este vendedor (pendientes + confirmados)
        $pedidosAsignados = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->count();
        
        // Pedidos de hoy (mis pedidos pendientes de hoy)
        $pedidosUrgentes = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->where('estado', 'pendiente')
            ->whereDate('fecha', Carbon::today())
            ->count();
        
        // Pedidos disponibles (NO asignados a nadie)
        $pedidosDisponibles = Pedido::where('sucursal_id', $sucursal_id)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->whereDoesntHave('responsables')
            ->count();
        
        // Ventas del mes (entregados)
        $ventasMes = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->where('estado', 'entregado')
            ->count();

        return response()->json([
            'pedidos_asignados' => $pedidosAsignados,
            'pedidos_urgentes' => $pedidosUrgentes,
            'pedidos_disponibles' => $pedidosDisponibles,
            'ventas_mes' => $ventasMes
        ]);
    }
}