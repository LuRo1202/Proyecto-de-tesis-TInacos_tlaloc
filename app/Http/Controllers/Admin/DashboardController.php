<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas
        $stats = [];
        
        // Total pedidos
        $stats['total_pedidos'] = Pedido::count();
        
        // Pedidos pendientes
        $stats['pedidos_pendientes'] = Pedido::where('estado', 'pendiente')->count();
        
        // Total productos activos
        $stats['total_productos'] = Producto::where('activo', true)->count();
        
        // Productos bajos en inventario (existencias <= 5)
        $stats['productos_bajos'] = DB::table('producto_sucursal')
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');
        
        // Ventas del mes
        $stats['ventas_mes'] = Pedido::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->where('pago_confirmado', true)
            ->sum('total');
        
        // Ventas del día
        $stats['ventas_hoy'] = Pedido::whereDate('fecha', today())
            ->where('pago_confirmado', true)
            ->sum('total');
        
        // Total clientes (teléfonos únicos)
        $stats['total_clientes'] = Pedido::distinct('cliente_telefono')->count('cliente_telefono');
        
        // Últimos pedidos
        $ultimosPedidos = Pedido::withCount('items')
            ->with('sucursal')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get()
            ->map(function($pedido) {
                $pedido->fecha = \Carbon\Carbon::parse($pedido->fecha);
                return $pedido;
            });
        // Productos más vendidos
        $productosMasVendidos = DB::table('pedido_items')
            ->select('producto_nombre', DB::raw('SUM(cantidad) as total_vendido'))
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->where('pedidos.estado', '!=', 'cancelado')
            ->groupBy('producto_nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(5)
            ->get();
        
        // Nombre del usuario actual
        $usuario_nombre = auth()->user()->nombre ?? 'Administrador';
        
        return view('admin.dashboard', compact('stats', 'ultimosPedidos', 'productosMasVendidos', 'usuario_nombre'));
    }
}