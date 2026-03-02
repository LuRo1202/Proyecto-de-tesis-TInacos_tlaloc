<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class VendedorController extends Controller
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

    public function index()
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

        // Estadísticas generales
        $estadisticas = $this->obtenerEstadisticas();

        // Vendedores de la sucursal
        $vendedores = Usuario::where('rol', 'vendedor')
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->orderByRaw('activo DESC, nombre ASC')
            ->get();

        // Estadísticas por vendedor
        $estadisticas_vendedores = $this->obtenerEstadisticasVendedores();

        return view('gerente.vendedores.index', compact(
            'estadisticas',
            'vendedores',
            'estadisticas_vendedores',
            'pedidos_pendientes_count'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'usuario' => 'required|string|max:50|unique:usuarios,usuario',
            'email' => 'required|email|max:100|unique:usuarios,email',
            'contrasena' => 'required|string|min:6',
            'confirmar_contrasena' => 'required|same:contrasena'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'usuario.required' => 'El nombre de usuario es obligatorio',
            'usuario.unique' => 'Este nombre de usuario ya está en uso',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.unique' => 'Este correo ya está registrado',
            'email.email' => 'Ingresa un correo válido',
            'contrasena.required' => 'La contraseña es obligatoria',
            'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres',
            'confirmar_contrasena.same' => 'Las contraseñas no coinciden'
        ]);

        try {
            DB::beginTransaction();

            // Crear el vendedor con Hash::make() de Laravel
            $vendedor = Usuario::create([
                'usuario' => $request->usuario,
                'contrasena_hash' => Hash::make($request->contrasena), // ✅ Hash seguro de Laravel
                'nombre' => $request->nombre,
                'email' => $request->email,
                'rol' => 'vendedor',
                'activo' => true,
                'fecha_creacion' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Asignar a la sucursal
            DB::table('usuario_sucursal')->insert([
                'usuario_id' => $vendedor->id,
                'sucursal_id' => $this->sucursalId,
                'fecha_asignacion' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()->route('gerente.vendedores')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Vendedor agregado!',
                    'message' => "El vendedor ha sido agregado correctamente a tu sucursal."
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear vendedor: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al crear el vendedor. Intenta de nuevo.'
                ]);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'contrasena' => 'nullable|string|min:6'
        ]);

        // Verificar que el vendedor pertenezca a la sucursal
        $vendedor = Usuario::where('id', $request->usuario_id)
            ->where('rol', 'vendedor')
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->first();

        if (!$vendedor) {
            return redirect()->back()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'No tienes permiso para editar este vendedor'
                ]);
        }

        // Verificar email único (excepto el actual)
        $emailExiste = Usuario::where('email', $request->email)
            ->where('id', '!=', $request->usuario_id)
            ->exists();

        if ($emailExiste) {
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Este correo ya está registrado por otro usuario'
                ]);
        }

        try {
            DB::beginTransaction();

            $data = [
                'nombre' => $request->nombre,
                'email' => $request->email,
                'updated_at' => now()
            ];

            if ($request->filled('contrasena')) {
                $data['contrasena_hash'] = Hash::make($request->contrasena); // ✅ Hash seguro de Laravel
            }

            $vendedor->update($data);

            DB::commit();

            return redirect()->route('gerente.vendedores')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Vendedor actualizado!',
                    'message' => 'El vendedor ha sido actualizado correctamente.'
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar vendedor: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al actualizar el vendedor'
                ]);
        }
    }

    public function toggleEstado(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
            'nuevo_estado' => 'required|in:0,1'
        ]);

        // Verificar que el vendedor pertenezca a la sucursal
        $vendedor = Usuario::where('id', $request->usuario_id)
            ->where('rol', 'vendedor')
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->first();

        if (!$vendedor) {
            return redirect()->back()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'No tienes permiso para modificar este vendedor'
                ]);
        }

        try {
            $vendedor->update(['activo' => $request->nuevo_estado]);
            
            $estado_texto = $request->nuevo_estado ? 'activado' : 'desactivado';

            return redirect()->route('gerente.vendedores')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Éxito!',
                    'message' => "Vendedor {$estado_texto} correctamente"
                ]);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Error al cambiar el estado del vendedor'
                ]);
        }
    }

    private function obtenerEstadisticas()
    {
        $total_vendedores = Usuario::where('rol', 'vendedor')
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->count();

        $vendedores_activos = Usuario::where('rol', 'vendedor')
            ->where('activo', true)
            ->whereHas('sucursales', function($q) {
                $q->where('sucursal_id', $this->sucursalId);
            })
            ->count();

        $total_pedidos_sucursal = Pedido::where('sucursal_id', $this->sucursalId)->count();

        $ventas_totales_sucursal = Pedido::where('sucursal_id', $this->sucursalId)
            ->where('estado', 'entregado')
            ->sum('total');

        return [
            'total_vendedores' => $total_vendedores,
            'vendedores_activos' => $vendedores_activos,
            'total_pedidos_sucursal' => $total_pedidos_sucursal,
            'ventas_totales_sucursal' => $ventas_totales_sucursal
        ];
    }

    private function obtenerEstadisticasVendedores()
    {
        $fecha_limite = Carbon::now()->subDays(30);

        return DB::table('usuarios as u')
            ->select(
                'u.id',
                'u.nombre',
                DB::raw('COUNT(DISTINCT pr.pedido_id) as total_pedidos_asignados'),
                DB::raw('SUM(CASE WHEN p.estado = "entregado" THEN p.total ELSE 0 END) as ventas_totales')
            )
            ->leftJoin('pedido_responsables as pr', 'u.id', '=', 'pr.usuario_id')
            ->leftJoin('pedidos as p', function($join) use ($fecha_limite) {
                $join->on('pr.pedido_id', '=', 'p.id')
                     ->where('p.sucursal_id', $this->sucursalId)
                     ->where('p.fecha', '>=', $fecha_limite);
            })
            ->join('usuario_sucursal as us', 'u.id', '=', 'us.usuario_id')
            ->where('us.sucursal_id', $this->sucursalId)
            ->where('u.rol', 'vendedor')
            ->where('u.activo', true)
            ->groupBy('u.id', 'u.nombre')
            ->having('total_pedidos_asignados', '>', 0)
            ->orHaving('ventas_totales', '>', 0)
            ->orderByRaw('ventas_totales DESC, total_pedidos_asignados DESC')
            ->get();
    }
}