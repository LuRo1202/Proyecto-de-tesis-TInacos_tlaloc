<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClienteController extends Controller
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

        // Parámetros de paginación y filtros
        $pagina = $request->get('pagina', 1);
        $por_pagina = 10;
        $offset = ($pagina - 1) * $por_pagina;

        $busqueda = $request->get('busqueda', '');
        $estado = $request->get('estado', '');
        $ciudad = $request->get('ciudad', '');

        // Obtener estadísticas generales
        $stats = $this->obtenerEstadisticas();

        // Obtener filtros (estados y ciudades) de la sucursal
        $estados = $this->obtenerEstados();
        $ciudades = $this->obtenerCiudades();

        // Obtener clientes paginados
        $clientesData = $this->obtenerClientes($busqueda, $estado, $ciudad, $offset, $por_pagina);
        $clientes = $clientesData['clientes'];
        $total_clientes = $clientesData['total'];
        $total_paginas = ceil($total_clientes / $por_pagina);

        return view('gerente.clientes.index', compact(
            'pedidos_pendientes_count',
            'productos_bajos_count',
            'pagina',
            'busqueda',
            'estado',
            'ciudad',
            'stats',
            'estados',
            'ciudades',
            'clientes',
            'total_clientes',
            'total_paginas'
        ));
    }

    public function historial(Request $request)
    {
        $telefono = $request->get('telefono');
        
        if (!$telefono) {
            return redirect()->route('gerente.clientes')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Teléfono no proporcionado'
                ]);
        }

        // Obtener datos del cliente
        $cliente = Pedido::select('cliente_nombre', 'cliente_telefono', 'cliente_direccion', 'cliente_ciudad', 'cliente_estado')
            ->where('cliente_telefono', $telefono)
            ->where('sucursal_id', $this->sucursalId)
            ->first();

        if (!$cliente) {
            return redirect()->route('gerente.clientes')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Cliente no encontrado en tu sucursal'
                ]);
        }

        // Obtener historial de pedidos del cliente
        $pedidos = Pedido::where('cliente_telefono', $telefono)
            ->where('sucursal_id', $this->sucursalId)
            ->orderBy('fecha', 'DESC')
            ->get()
            ->map(function($pedido) {
                $pedido->fecha = Carbon::parse($pedido->fecha);
                $pedido->fecha_entrega = $pedido->fecha_entrega ? Carbon::parse($pedido->fecha_entrega) : null;
                return $pedido;
            });

        // Estadísticas del cliente
        $stats_cliente = [
            'total_pedidos' => $pedidos->count(),
            'total_gastado' => $pedidos->sum('total'),
            'pedidos_entregados' => $pedidos->where('estado', 'entregado')->count(),
            'pedidos_pendientes' => $pedidos->where('estado', 'pendiente')->count()
        ];

        return view('gerente.clientes.historial', compact('cliente', 'pedidos', 'stats_cliente'));
    }

    public function reporteExcel(Request $request)
    {
        $busqueda = $request->get('busqueda', '');
        $estado = $request->get('estado', '');
        $ciudad = $request->get('ciudad', '');

        $clientesData = $this->obtenerClientes($busqueda, $estado, $ciudad, 0, 9999);
        $clientes = $clientesData['clientes'];

        // Aquí iría la lógica para generar el Excel
        // Por ahora redirigimos con mensaje
        return redirect()->route('gerente.clientes')
            ->with('swal', [
                'type' => 'info',
                'title' => 'Reporte',
                'message' => 'La funcionalidad de exportar a Excel estará disponible próximamente.'
            ]);
    }

    private function obtenerEstadisticas()
    {
        $stats = Pedido::select(
                DB::raw('COUNT(DISTINCT cliente_telefono) as total_clientes'),
                DB::raw('AVG(total) as promedio_pedido'),
                DB::raw('SUM(total) as total_ventas')
            )
            ->where('sucursal_id', $this->sucursalId)
            ->first();

        return [
            'total_clientes' => $stats->total_clientes ?? 0,
            'total_ventas' => $stats->total_ventas ?? 0,
            'promedio_pedido' => $stats->promedio_pedido ?? 0
        ];
    }

    private function obtenerEstados()
    {
        return Pedido::select('cliente_estado')
            ->where('sucursal_id', $this->sucursalId)
            ->whereNotNull('cliente_estado')
            ->where('cliente_estado', '!=', '')
            ->distinct()
            ->orderBy('cliente_estado')
            ->get();
    }

    private function obtenerCiudades()
    {
        return Pedido::select('cliente_ciudad')
            ->where('sucursal_id', $this->sucursalId)
            ->whereNotNull('cliente_ciudad')
            ->where('cliente_ciudad', '!=', '')
            ->distinct()
            ->orderBy('cliente_ciudad')
            ->get();
    }

    private function obtenerClientes($busqueda, $estado, $ciudad, $offset, $limit)
    {
        $query = Pedido::select(
                'cliente_nombre',
                'cliente_telefono',
                'cliente_ciudad',
                'cliente_estado',
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_gastado'),
                DB::raw('MAX(fecha) as ultimo_pedido')
            )
            ->where('sucursal_id', $this->sucursalId);

        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('cliente_nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cliente_telefono', 'LIKE', "%{$busqueda}%");
            });
        }

        if ($estado) {
            $query->where('cliente_estado', $estado);
        }

        if ($ciudad) {
            $query->where('cliente_ciudad', $ciudad);
        }

        // Primero obtenemos el total
        $total = $query->count(DB::raw('DISTINCT cliente_telefono'));

        // Luego obtenemos los clientes paginados
        $clientes = $query->groupBy('cliente_telefono', 'cliente_nombre', 'cliente_ciudad', 'cliente_estado')
            ->orderBy('ultimo_pedido', 'DESC')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function($cliente) {
                $cliente->ultimo_pedido = $cliente->ultimo_pedido ? Carbon::parse($cliente->ultimo_pedido) : null;
                return $cliente;
            });

        return [
            'clientes' => $clientes,
            'total' => $total
        ];
    }
}