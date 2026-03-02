<?php
namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\PedidoResponsable;
use Carbon\Carbon;

class ClienteController extends Controller
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

        // Verificar si el usuario tiene pedidos asignados
        $tienePedidosAsignados = PedidoResponsable::where('usuario_id', $usuario_id)->exists();

        // Determinar tipo de vista
        $tipo_vista = $tienePedidosAsignados ? 'mis_clientes' : 'sucursal_clientes';

        // Búsqueda
        $busqueda = $request->busqueda ?? '';

        // Obtener clientes según el caso
        $clientes = $this->getClientes($usuario_id, $sucursal_id, $tienePedidosAsignados, $busqueda);
        
        // Obtener estadísticas
        $stats = $this->getEstadisticas($usuario_id, $sucursal_id, $tienePedidosAsignados);

        return view('vendedor.clientes.index', compact(
            'clientes',
            'stats',
            'busqueda',
            'sucursal',
            'sucursalNombre',
            'tipo_vista'
        ));
    }

    private function getClientes($usuario_id, $sucursal_id, $tienePedidosAsignados, $busqueda = '')
    {
        $query = Pedido::where('sucursal_id', $sucursal_id);

        if ($tienePedidosAsignados) {
            $query->whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            });
        }

        $clientes = $query->select(
                'cliente_nombre',
                'cliente_telefono',
                'cliente_direccion',
                'cliente_ciudad',
                'cliente_estado',
                DB::raw('COUNT(DISTINCT id) as total_pedidos'),
                DB::raw('SUM(CASE WHEN estado = "entregado" AND pago_confirmado = 1 THEN total ELSE 0 END) as total_comprado'),
                DB::raw('MAX(fecha) as ultima_compra')
            )
            ->groupBy('cliente_telefono', 'cliente_nombre', 'cliente_direccion', 'cliente_ciudad', 'cliente_estado')
            ->orderBy('total_comprado', 'desc')
            ->orderBy('total_pedidos', 'desc')
            ->get();

        // Filtrar por búsqueda si existe
        if (!empty($busqueda)) {
            $clientes = $clientes->filter(function($cliente) use ($busqueda) {
                return stripos($cliente->cliente_nombre, $busqueda) !== false ||
                       stripos($cliente->cliente_telefono, $busqueda) !== false ||
                       stripos($cliente->cliente_ciudad, $busqueda) !== false;
            });
        }

        return $clientes;
    }

    private function getEstadisticas($usuario_id, $sucursal_id, $tienePedidosAsignados)
    {
        $query = Pedido::where('sucursal_id', $sucursal_id);

        if ($tienePedidosAsignados) {
            $query->whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            });
        }

        $stats = $query->select(
                DB::raw('COUNT(DISTINCT cliente_telefono) as total_clientes'),
                DB::raw('COUNT(DISTINCT CASE WHEN DATE(fecha) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN cliente_telefono END) as clientes_30dias'),
                DB::raw('AVG(CASE WHEN estado = "entregado" AND pago_confirmado = 1 THEN total ELSE NULL END) as ticket_promedio')
            )
            ->first();

        return [
            'total_clientes' => $stats->total_clientes ?? 0,
            'clientes_30dias' => $stats->clientes_30dias ?? 0,
            'ticket_promedio' => $stats->ticket_promedio ?? 0
        ];
    }

    public function historial($telefono)
    {
        $usuario = Auth::user();
        
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'No tienes una sucursal asignada.');
        }

        // Obtener pedidos del cliente
        $pedidos = Pedido::where('sucursal_id', $sucursal->id)
            ->where('cliente_telefono', $telefono)
            ->with(['items', 'responsables.usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        if ($pedidos->isEmpty()) {
            return redirect()->route('vendedor.clientes.index')
                ->with('error', 'Cliente no encontrado.');
        }

        $cliente = [
            'nombre' => $pedidos->first()->cliente_nombre,
            'telefono' => $telefono,
            'direccion' => $pedidos->first()->cliente_direccion,
            'ciudad' => $pedidos->first()->cliente_ciudad,
            'estado' => $pedidos->first()->cliente_estado
        ];

        return view('vendedor.clientes.historial', compact('pedidos', 'cliente'));
    }
}