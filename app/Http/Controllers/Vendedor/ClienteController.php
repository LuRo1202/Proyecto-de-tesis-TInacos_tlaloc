<?php
namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\PedidoResponsable;
use App\Models\Cliente; // ← IMPORTANTE
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

        // Obtener clientes según el caso (CORREGIDO - SIN DUPLICADOS)
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

    /**
     * CORREGIDO: Obtener clientes SIN duplicados
     * Ahora agrupa correctamente por teléfono y toma el cliente_id de la tabla clientes
     */
    private function getClientes($usuario_id, $sucursal_id, $tienePedidosAsignados, $busqueda = '')
    {
        $query = Pedido::where('pedidos.sucursal_id', $sucursal_id);

        if ($tienePedidosAsignados) {
            $query->whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            });
        }

        // CORRECCIÓN: Usar subquery para obtener el último pedido de cada cliente
        $clientes = $query->join(
                DB::raw('(SELECT MAX(id) as max_id, cliente_telefono 
                         FROM pedidos 
                         WHERE sucursal_id = ' . $sucursal_id . ' 
                         GROUP BY cliente_telefono) as ultimos_pedidos'),
                function($join) {
                    $join->on('pedidos.id', '=', 'ultimos_pedidos.max_id');
                }
            )
            ->leftJoin('clientes', 'pedidos.cliente_telefono', '=', 'clientes.telefono')
            ->select(
                'pedidos.cliente_nombre',
                'pedidos.cliente_telefono',
                'pedidos.cliente_direccion',
                'pedidos.cliente_ciudad',
                'pedidos.cliente_estado',
                'clientes.email',
                'clientes.id as cliente_id',
                'clientes.created_at as fecha_registro',
                DB::raw('(SELECT COUNT(*) FROM pedidos p2 
                          WHERE p2.cliente_telefono = pedidos.cliente_telefono 
                          AND p2.sucursal_id = ' . $sucursal_id . ') as total_pedidos'),
                DB::raw('(SELECT SUM(CASE WHEN p3.estado = "entregado" AND p3.pago_confirmado = 1 THEN p3.total ELSE 0 END) 
                          FROM pedidos p3 
                          WHERE p3.cliente_telefono = pedidos.cliente_telefono 
                          AND p3.sucursal_id = ' . $sucursal_id . ') as total_comprado'),
                DB::raw('(SELECT MAX(p4.fecha) FROM pedidos p4 
                          WHERE p4.cliente_telefono = pedidos.cliente_telefono 
                          AND p4.sucursal_id = ' . $sucursal_id . ') as ultima_compra')
            )
            ->orderBy('total_comprado', 'desc')
            ->orderBy('total_pedidos', 'desc')
            ->get();

        // Filtrar por búsqueda si existe
        if (!empty($busqueda)) {
            $clientes = $clientes->filter(function($cliente) use ($busqueda) {
                return stripos($cliente->cliente_nombre, $busqueda) !== false ||
                       stripos($cliente->cliente_telefono, $busqueda) !== false ||
                       stripos($cliente->cliente_ciudad ?? '', $busqueda) !== false ||
                       ($cliente->email && stripos($cliente->email, $busqueda) !== false);
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
                DB::raw('AVG(CASE WHEN estado = "entregado" AND pago_confirmado = 1 THEN total ELSE NULL END) as ticket_promedio'),
                DB::raw('SUM(CASE WHEN estado = "entregado" AND pago_confirmado = 1 THEN total ELSE 0 END) as total_ventas')
            )
            ->first();

        return [
            'total_clientes' => $stats->total_clientes ?? 0,
            'clientes_30dias' => $stats->clientes_30dias ?? 0,
            'ticket_promedio' => $stats->ticket_promedio ?? 0,
            'total_ventas' => $stats->total_ventas ?? 0
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

        // Obtener datos de la tabla clientes si existe
        $clienteData = Cliente::where('telefono', $telefono)->first();

        $cliente = [
            'nombre' => $pedidos->first()->cliente_nombre,
            'telefono' => $telefono,
            'direccion' => $pedidos->first()->cliente_direccion,
            'ciudad' => $pedidos->first()->cliente_ciudad,
            'estado' => $pedidos->first()->cliente_estado,
            'email' => $clienteData->email ?? null,
            'fecha_registro' => $clienteData->created_at ?? null
        ];

        return view('vendedor.clientes.historial', compact('pedidos', 'cliente'));
    }

    /**
     * IMPORTANTE: Esta función la usa el create de pedidos
     * NO MODIFICAR
     */
    public function buscar(Request $request)
    {
        $busqueda = $request->get('busqueda');
        
        if (empty($busqueda) || strlen($busqueda) < 3) {
            return response()->json([]);
        }
        
        $clientes = Cliente::where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('telefono', 'LIKE', "%{$busqueda}%")
                    ->orWhere('email', 'LIKE', "%{$busqueda}%")
                    ->limit(10)
                    ->get(['id', 'nombre', 'telefono', 'email', 'direccion', 'ciudad', 'estado', 'codigo_postal']);
        
        return response()->json($clientes);
    }
}