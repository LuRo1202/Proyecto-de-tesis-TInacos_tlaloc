<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Sucursal;
use App\Models\PedidoItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $sucursal;
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

    public function index()
    {
        // Contadores para el sidebar (solo de esta sucursal)
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

        // ESTADÍSTICAS DE LA SUCURSAL
        $stats = [];

        // Total pedidos de la sucursal
        $stats['total_pedidos'] = Pedido::where('sucursal_id', $this->sucursalId)->count();

        // Pedidos pendientes
        $stats['pedidos_pendientes'] = $pedidos_pendientes_count;

        // Total productos activos (global)
        $stats['total_productos'] = Producto::where('activo', true)->count();

        // Productos bajos en inventario (de esta sucursal)
        $stats['productos_bajos'] = $productos_bajos_count;

        // Ventas del mes (de esta sucursal)
        $stats['ventas_mes'] = Pedido::where('sucursal_id', $this->sucursalId)
            ->where('pago_confirmado', true)
            ->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->sum('total') ?? 0;

        // Ventas del día (de esta sucursal)
        $stats['ventas_hoy'] = Pedido::where('sucursal_id', $this->sucursalId)
            ->where('pago_confirmado', true)
            ->whereDate('fecha', Carbon::today())
            ->sum('total') ?? 0;

        // Total clientes únicos de la sucursal
        $stats['total_clientes'] = Pedido::where('sucursal_id', $this->sucursalId)
            ->distinct('cliente_telefono')
            ->count('cliente_telefono');

        // Vendedores de la sucursal
        $vendedores = Usuario::where('rol', 'vendedor')
            ->where('activo', true)
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->orderBy('nombre')
            ->get();
            
        $stats['total_vendedores'] = $vendedores->count();

        // Últimos pedidos de la sucursal
        $ultimosPedidos = Pedido::withCount('items')
            ->with('sucursal')
            ->where('sucursal_id', $this->sucursalId)
            ->orderBy('fecha', 'desc')
            ->limit(10)
            ->get()
            ->map(function($pedido) {
                $pedido->fecha = Carbon::parse($pedido->fecha);
                return $pedido;
            });

        // Productos más vendidos en la sucursal
        $productosMasVendidos = DB::table('pedido_items as pi')
            ->join('pedidos as p', 'pi.pedido_id', '=', 'p.id')
            ->where('p.sucursal_id', $this->sucursalId)
            ->where('p.estado', '!=', 'cancelado')
            ->select(
                'pi.producto_nombre',
                DB::raw('SUM(pi.cantidad) as total_vendido')
            )
            ->groupBy('pi.producto_nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(5)
            ->get();

        // Obtener nombre de la sucursal para la vista
        $sucursal = Sucursal::find($this->sucursalId);

        return view('gerente.dashboard', compact(
            'stats',
            'ultimosPedidos',
            'productosMasVendidos',
            'vendedores',
            'sucursal'
        ));
    }
}