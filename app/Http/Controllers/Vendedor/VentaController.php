<?php
namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\PedidoResponsable;
use App\Models\PedidoItem;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $usuario = Auth::user();
        $usuario_id = $usuario->id;
        
        // Obtener sucursal del vendedor
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }

        $sucursalNombre = $sucursal->nombre;
        $sucursal_id = $sucursal->id;

        // Parámetros de filtro
        $tipo_reporte = $request->tipo ?? 'mensual';

        // Verificar si el usuario tiene pedidos asignados
        $tienePedidosAsignados = PedidoResponsable::where('usuario_id', $usuario_id)->exists();

        // Determinar tipo de vista
        $tipo_vista = $tienePedidosAsignados ? 'mis_pedidos' : 'sucursal';

        // Obtener estadísticas según el caso
        $estadisticas = $this->getEstadisticas($usuario_id, $sucursal_id, $tienePedidosAsignados);
        
        // Ventas por mes (CORREGIDO)
        $ventas_meses = $this->getVentasMeses($usuario_id, $sucursal_id, $tienePedidosAsignados);
        
        // Productos más vendidos
        $productos_top = $this->getProductosTop($usuario_id, $sucursal_id, $tienePedidosAsignados);
        
        // Clientes más frecuentes
        $clientes_top = $this->getClientesTop($usuario_id, $sucursal_id, $tienePedidosAsignados);
        
        // Ventas diarias del mes actual
        $ventas_diarias = $this->getVentasDiarias($usuario_id, $sucursal_id, $tienePedidosAsignados);

        // Calcular comisiones (5%)
        $comisiones_totales = $estadisticas['ventas_totales'] * 0.05;

        // Meta de ventas (ejemplo)
        $meta_mensual = 50000;
        $porcentaje_meta = $meta_mensual > 0 ? min(100, ($estadisticas['ventas_totales'] / $meta_mensual) * 100) : 0;

        return view('vendedor.ventas.index', compact(
            'estadisticas',
            'ventas_meses',
            'productos_top',
            'clientes_top',
            'ventas_diarias',
            'comisiones_totales',
            'sucursal',
            'sucursalNombre',
            'tipo_vista',
            'tipo_reporte',
            'meta_mensual',
            'porcentaje_meta'
        ));
    }

    private function getEstadisticas($usuario_id, $sucursal_id, $tienePedidosAsignados)
    {
        $query = Pedido::where('sucursal_id', $sucursal_id);

        if ($tienePedidosAsignados) {
            $query->whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            });
        }

        return [
            'total_pedidos' => (clone $query)->count(),
            'ventas_totales' => (clone $query)
                ->where('estado', 'entregado')
                ->where('pago_confirmado', 1)
                ->sum('total') ?? 0,
            'promedio_venta' => (clone $query)
                ->where('estado', 'entregado')
                ->where('pago_confirmado', 1)
                ->avg('total') ?? 0,
            'clientes_unicos' => (clone $query)
                ->where('estado', 'entregado')
                ->where('pago_confirmado', 1)
                ->distinct('cliente_telefono')
                ->count('cliente_telefono')
        ];
    }

    private function getVentasMeses($usuario_id, $sucursal_id, $tienePedidosAsignados)
    {
        $query = Pedido::where('sucursal_id', $sucursal_id);

        if ($tienePedidosAsignados) {
            $query->whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            });
        }

        return $query->select(
                DB::raw('DATE_FORMAT(fecha, "%Y-%m") as mes'),
                DB::raw('YEAR(fecha) as anio'),
                DB::raw('MONTH(fecha) as mes_numero'),
                DB::raw('COUNT(DISTINCT id) as pedidos'),
                DB::raw('SUM(CASE WHEN estado = "entregado" AND pago_confirmado = 1 THEN total ELSE 0 END) as ventas')
            )
            ->groupBy(DB::raw('DATE_FORMAT(fecha, "%Y-%m")'), DB::raw('YEAR(fecha)'), DB::raw('MONTH(fecha)'))
            ->orderBy('mes', 'desc')
            ->limit(12)
            ->get()
            ->map(function($item) {
                // Convertir el número de mes a nombre en español
                $meses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ];
                
                $item->mes_nombre = $meses[$item->mes_numero] . ' ' . $item->anio;
                return $item;
            });
    }

    private function getProductosTop($usuario_id, $sucursal_id, $tienePedidosAsignados)
    {
        $query = PedidoItem::join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->where('pedidos.sucursal_id', $sucursal_id)
            ->where('pedidos.estado', 'entregado')
            ->where('pedidos.pago_confirmado', 1);

        if ($tienePedidosAsignados) {
            $query->join('pedido_responsables', 'pedidos.id', '=', 'pedido_responsables.pedido_id')
                ->where('pedido_responsables.usuario_id', $usuario_id);
        }

        return $query->select(
                'pedido_items.producto_nombre',
                DB::raw('SUM(pedido_items.cantidad) as cantidad_vendida'),
                DB::raw('SUM(pedido_items.cantidad * pedido_items.precio) as total_vendido')
            )
            ->groupBy('pedido_items.producto_nombre')
            ->orderBy('cantidad_vendida', 'desc')
            ->limit(10)
            ->get();
    }

    private function getClientesTop($usuario_id, $sucursal_id, $tienePedidosAsignados)
    {
        $query = Pedido::where('sucursal_id', $sucursal_id)
            ->where('estado', 'entregado')
            ->where('pago_confirmado', 1);

        if ($tienePedidosAsignados) {
            $query->whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            });
        }

        return $query->select(
                'cliente_nombre',
                'cliente_telefono',
                DB::raw('COUNT(DISTINCT id) as pedidos'),
                DB::raw('SUM(total) as total_gastado')
            )
            ->groupBy('cliente_telefono', 'cliente_nombre')
            ->orderBy('pedidos', 'desc')
            ->orderBy('total_gastado', 'desc')
            ->limit(10)
            ->get();
    }

    private function getVentasDiarias($usuario_id, $sucursal_id, $tienePedidosAsignados)
    {
        $query = Pedido::where('sucursal_id', $sucursal_id)
            ->whereYear('fecha', Carbon::now()->year)
            ->whereMonth('fecha', Carbon::now()->month);

        if ($tienePedidosAsignados) {
            $query->whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            });
        }

        return $query->select(
                DB::raw('DATE(fecha) as fecha'),
                DB::raw('COUNT(DISTINCT id) as pedidos'),
                DB::raw('SUM(CASE WHEN estado = "entregado" AND pago_confirmado = 1 THEN total ELSE 0 END) as ventas')
            )
            ->groupBy('fecha')
            ->orderBy('fecha', 'desc')
            ->get();
    }
}