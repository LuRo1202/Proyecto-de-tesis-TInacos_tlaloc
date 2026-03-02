<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Usuario;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReporteController extends Controller
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
        
        // Obtener la sucursal del gerente
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
        
        // Guardar en sesión
        session([
            'sucursal_nombre' => $this->sucursalNombre,
            'sucursal_id' => $this->sucursalId
        ]);
    }

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

        // Obtener parámetros de filtro
        $fecha_inicio = $request->get('fecha_inicio', date('Y-m-01'));
        $fecha_fin = $request->get('fecha_fin', date('Y-m-d'));
        $tipo_reporte = $request->get('tipo_reporte', 'ventas');

        // Reporte de Ventas
        $datos_ventas = $this->obtenerReporteVentas($fecha_inicio, $fecha_fin);

        // Productos más vendidos
        $top_productos = $this->obtenerTopProductos($fecha_inicio, $fecha_fin);

        // Ventas por vendedor
        $ventas_por_vendedor = $this->obtenerVentasPorVendedor($fecha_inicio, $fecha_fin);

        // Ventas por método de pago
        $ventas_por_metodo = $this->obtenerVentasPorMetodo($fecha_inicio, $fecha_fin);

        // Ventas por estado
        $ventas_por_estado = $this->obtenerVentasPorEstado($fecha_inicio, $fecha_fin);

        // Estadísticas generales
        $estadisticas = $this->obtenerEstadisticasGenerales($fecha_inicio, $fecha_fin);

        return view('gerente.reportes.index', compact(
            'fecha_inicio',
            'fecha_fin',
            'tipo_reporte',
            'datos_ventas',
            'top_productos',
            'ventas_por_vendedor',
            'ventas_por_metodo',
            'ventas_por_estado',
            'estadisticas',
            'pedidos_pendientes_count'
        ));
    }

    private function obtenerReporteVentas($fecha_inicio, $fecha_fin)
    {
        return Pedido::select(
                DB::raw('DATE(fecha) as fecha_dia'),
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas'),
                DB::raw('AVG(total) as promedio_venta')
            )
            ->whereDate('fecha', '>=', $fecha_inicio)
            ->whereDate('fecha', '<=', $fecha_fin)
            ->where('pago_confirmado', true)
            ->where('sucursal_id', $this->sucursalId)
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('fecha_dia', 'DESC')
            ->limit(30)
            ->get()
            ->map(function($item) {
                $item->fecha_dia = Carbon::parse($item->fecha_dia);
                return $item;
            });
    }

    private function obtenerTopProductos($fecha_inicio, $fecha_fin)
    {
        return DB::table('pedido_items as pi')
            ->join('productos as p', 'pi.producto_id', '=', 'p.id')
            ->join('pedidos as pd', 'pi.pedido_id', '=', 'pd.id')
            ->whereDate('pd.fecha', '>=', $fecha_inicio)
            ->whereDate('pd.fecha', '<=', $fecha_fin)
            ->where('pd.pago_confirmado', true)
            ->where('pd.sucursal_id', $this->sucursalId)
            ->select(
                'p.codigo',
                'p.nombre',
                'p.precio',
                'p.litros',
                DB::raw('SUM(pi.cantidad) as total_vendido'),
                DB::raw('SUM(pi.cantidad * pi.precio) as total_ingresos')
            )
            ->groupBy('p.id', 'p.codigo', 'p.nombre', 'p.precio', 'p.litros')
            ->orderBy('total_vendido', 'DESC')
            ->limit(10)
            ->get();
    }

    private function obtenerVentasPorVendedor($fecha_inicio, $fecha_fin)
    {
        return DB::table('usuarios as u')
            ->join('usuario_sucursal as us', 'u.id', '=', 'us.usuario_id')
            ->leftJoin('pedido_responsables as pr', 'u.id', '=', 'pr.usuario_id')
            ->leftJoin('pedidos as p', function($join) use ($fecha_inicio, $fecha_fin) {
                $join->on('pr.pedido_id', '=', 'p.id')
                     ->whereDate('p.fecha', '>=', $fecha_inicio)
                     ->whereDate('p.fecha', '<=', $fecha_fin)
                     ->where('p.sucursal_id', $this->sucursalId);
            })
            ->where('us.sucursal_id', $this->sucursalId)
            ->where('u.rol', 'vendedor')
            ->where('u.activo', true)
            ->select(
                'u.id as vendedor_id',
                'u.nombre as vendedor_nombre',
                'u.usuario',
                DB::raw('COUNT(DISTINCT pr.pedido_id) as total_pedidos'),
                DB::raw('SUM(CASE WHEN p.pago_confirmado = 1 THEN p.total ELSE 0 END) as total_ventas'),
                DB::raw('AVG(CASE WHEN p.pago_confirmado = 1 THEN p.total ELSE NULL END) as promedio_venta')
            )
            ->groupBy('u.id', 'u.nombre', 'u.usuario')
            ->having('total_ventas', '>', 0)
            ->orderBy('total_ventas', 'DESC')
            ->limit(10)
            ->get();
    }

    private function obtenerVentasPorMetodo($fecha_inicio, $fecha_fin)
    {
        return Pedido::select(
                'metodo_pago',
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas')
            )
            ->whereDate('fecha', '>=', $fecha_inicio)
            ->whereDate('fecha', '<=', $fecha_fin)
            ->where('pago_confirmado', true)
            ->where('sucursal_id', $this->sucursalId)
            ->groupBy('metodo_pago')
            ->orderBy('total_ventas', 'DESC')
            ->get();
    }

    private function obtenerVentasPorEstado($fecha_inicio, $fecha_fin)
    {
        return Pedido::select(
                'cliente_estado',
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas')
            )
            ->whereDate('fecha', '>=', $fecha_inicio)
            ->whereDate('fecha', '<=', $fecha_fin)
            ->where('pago_confirmado', true)
            ->where('sucursal_id', $this->sucursalId)
            ->groupBy('cliente_estado')
            ->orderBy('total_ventas', 'DESC')
            ->get();
    }

    private function obtenerEstadisticasGenerales($fecha_inicio, $fecha_fin)
    {
        $stats = Pedido::select(
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas'),
                DB::raw('AVG(total) as promedio_venta'),
                DB::raw('COUNT(DISTINCT cliente_telefono) as clientes_unicos')
            )
            ->whereDate('fecha', '>=', $fecha_inicio)
            ->whereDate('fecha', '<=', $fecha_fin)
            ->where('pago_confirmado', true)
            ->where('sucursal_id', $this->sucursalId)
            ->first();

        return [
            'total_pedidos' => $stats->total_pedidos ?? 0,
            'total_ventas' => $stats->total_ventas ?? 0,
            'promedio_venta' => $stats->promedio_venta ?? 0,
            'clientes_unicos' => $stats->clientes_unicos ?? 0
        ];
    }

    public function exportarExcel(Request $request)
    {
        // Aquí iría la lógica para exportar a Excel
        // Por ahora solo redirigimos con un mensaje
        return redirect()->route('gerente.reportes')
            ->with('swal', [
                'type' => 'info',
                'title' => 'Exportación',
                'message' => 'La funcionalidad de exportar a Excel estará disponible próximamente.'
            ]);
    }
}