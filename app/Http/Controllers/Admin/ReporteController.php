<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use App\Helpers\SucursalHelper;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la sucursal actual según el usuario logueado
        $sucursal = SucursalHelper::getSucursalActual();
        
        // Obtener todas las sucursales (para el filtro)
        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();

        // Contadores para el sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')->count();
        $productos_bajos_count = DB::table('producto_sucursal')
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
        $sucursal_id = $request->get('sucursal_id', $sucursal ? $sucursal->id : 'todas');

        // ✅ CORREGIDO: Construir query base con filtro por sucursal usando sucursal_id
        $queryPedidos = Pedido::where('pago_confirmado', true)
            ->whereBetween(DB::raw('DATE(fecha)'), [$fecha_inicio, $fecha_fin]);

        // ✅ CORREGIDO: Filtrar por sucursal usando sucursal_id
        if ($sucursal_id !== 'todas' && is_numeric($sucursal_id)) {
            $queryPedidos->where('sucursal_id', $sucursal_id);
        } elseif ($sucursal && $sucursal->id && $sucursal_id === 'todas') {
            // Si el usuario no es admin, solo ve su sucursal
            if (auth()->user()->rol !== 'admin') {
                $queryPedidos->where('sucursal_id', $sucursal->id);
            }
        }

        // Estadísticas generales
        $estadisticas = [
            'total_pedidos' => (clone $queryPedidos)->count(),
            'total_ventas' => (clone $queryPedidos)->sum('total') ?? 0,
            'promedio_venta' => (clone $queryPedidos)->avg('total') ?? 0,
            'clientes_unicos' => (clone $queryPedidos)->distinct('cliente_telefono')->count('cliente_telefono')
        ];

        // Reporte de Ventas diarias
        $ventasQuery = (clone $queryPedidos)
            ->select(
                DB::raw('DATE(fecha) as fecha_dia'),
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas'),
                DB::raw('AVG(total) as promedio_venta')
            )
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('fecha_dia', 'desc')
            ->limit(30);

        $datos_ventas = $ventasQuery->get();

        // ✅ CORREGIDO: Productos más vendidos con sucursal_id
        $productosQuery = DB::table('pedido_items as pi')
            ->join('pedidos as p', 'pi.pedido_id', '=', 'p.id')
            ->leftJoin('productos as prod', 'pi.producto_id', '=', 'prod.id')
            ->whereBetween(DB::raw('DATE(p.fecha)'), [$fecha_inicio, $fecha_fin])
            ->where('p.pago_confirmado', true)
            ->whereNotNull('pi.producto_id');

        // ✅ CORREGIDO: Aplicar filtro de sucursal a productos
        if ($sucursal_id !== 'todas' && is_numeric($sucursal_id)) {
            $productosQuery->where('p.sucursal_id', $sucursal_id);
        } elseif ($sucursal && $sucursal->id && $sucursal_id === 'todas') {
            if (auth()->user()->rol !== 'admin') {
                $productosQuery->where('p.sucursal_id', $sucursal->id);
            }
        }

        $top_productos = $productosQuery
            ->select(
                'prod.codigo',
                'prod.nombre',
                'prod.precio',
                'prod.litros',
                DB::raw('COALESCE(SUM(pi.cantidad), 0) as total_vendido'),
                DB::raw('COALESCE(SUM(pi.cantidad * pi.precio), 0) as total_ingresos')
            )
            ->groupBy('pi.producto_id', 'prod.codigo', 'prod.nombre', 'prod.precio', 'prod.litros')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        // Ventas por estado
        $estadosQuery = (clone $queryPedidos)
            ->select(
                'cliente_estado',
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas')
            )
            ->whereNotNull('cliente_estado')
            ->where('cliente_estado', '!=', '')
            ->groupBy('cliente_estado')
            ->orderBy('total_ventas', 'desc');

        $ventas_por_estado = $estadosQuery->get();

        // Métodos de pago
        $metodosQuery = (clone $queryPedidos)
            ->select(
                DB::raw("CASE 
                    WHEN metodo_pago = 'en_linea' THEN 'en_linea'
                    WHEN metodo_pago = 'manual' THEN 'manual'
                    ELSE 'otro'
                END as metodo_pago"),
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas')
            )
            ->groupBy(DB::raw("CASE 
                WHEN metodo_pago = 'en_linea' THEN 'en_linea'
                WHEN metodo_pago = 'manual' THEN 'manual'
                ELSE 'otro'
            END"))
            ->orderBy('total_ventas', 'desc');

        $ventas_por_metodo = $metodosQuery->get();

        // ✅ CORREGIDO: Ventas por sucursal usando sucursal_id
        $sucursalesQuery = (clone $queryPedidos)
            ->select(
                'sucursal_id',
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas')
            )
            ->groupBy('sucursal_id')
            ->orderBy('total_ventas', 'desc');

        $ventas_por_sucursal = $sucursalesQuery->get()
            ->map(function($item) {
                $sucursal = Sucursal::find($item->sucursal_id);
                $item->sucursal = $sucursal ? $sucursal->nombre : 'Sin asignar';
                return $item;
            });

        // ✅ CORREGIDO: Vendedores más activos con sucursal_id
        $vendedoresQuery = DB::table('pedido_responsables as pr')
            ->join('pedidos as p', 'pr.pedido_id', '=', 'p.id')
            ->leftJoin('usuarios as u', 'pr.usuario_id', '=', 'u.id')
            ->whereBetween(DB::raw('DATE(p.fecha)'), [$fecha_inicio, $fecha_fin])
            ->where('p.pago_confirmado', true);

        // ✅ CORREGIDO: Aplicar filtro de sucursal a vendedores
        if ($sucursal_id !== 'todas' && is_numeric($sucursal_id)) {
            $vendedoresQuery->where('p.sucursal_id', $sucursal_id);
        } elseif ($sucursal && $sucursal->id && $sucursal_id === 'todas') {
            if (auth()->user()->rol !== 'admin') {
                $vendedoresQuery->where('p.sucursal_id', $sucursal->id);
            }
        }

        $top_vendedores = $vendedoresQuery
            ->select(
                'u.nombre as vendedor',
                DB::raw('COUNT(DISTINCT pr.pedido_id) as total_pedidos_asignados'),
                DB::raw('COALESCE(SUM(p.total), 0) as total_ventas_gestionadas')
            )
            ->groupBy('u.id', 'u.nombre')
            ->orderBy('total_ventas_gestionadas', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reportes.index', compact(
            'datos_ventas',
            'top_productos',
            'ventas_por_estado',
            'ventas_por_metodo',
            'ventas_por_sucursal',
            'top_vendedores',
            'estadisticas',
            'fecha_inicio',
            'fecha_fin',
            'tipo_reporte',
            'sucursales',
            'sucursal_id',
            'sucursal'
        ));
    }
}