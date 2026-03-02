<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Models\PedidoResponsable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcesarAccionController extends Controller
{
    protected $usuarioId;
    protected $usuarioNombre;
    protected $sucursalNombre;

    public function __construct()
    {
        $user = auth()->user();
        
        if (!$user || $user->rol !== 'vendedor') {
            abort(403, 'Acceso no autorizado');
        }
        
        $this->usuarioId = $user->id;
        $this->usuarioNombre = $user->nombre;
        
        // Obtener sucursal del vendedor
        $usuarioSucursal = DB::table('usuario_sucursal')
            ->where('usuario_id', $user->id)
            ->first();
            
        $this->sucursalNombre = DB::table('sucursales')
            ->where('id', $usuarioSucursal->sucursal_id)
            ->value('nombre');
    }

    public function procesar(Request $request, $accion, $id)
    {
        // Verificar que el pedido pertenezca a la sucursal del vendedor
        $pedido = Pedido::where('id', $id)
            ->where('sucursal_asignada', $this->sucursalNombre)
            ->first();

        if (!$pedido) {
            return redirect()->route('vendedor.pedidos')
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'No tienes acceso a este pedido.'
                ]);
        }

        try {
            DB::beginTransaction();
            
            $estado_anterior = $pedido->estado;
            
            switch ($accion) {
                case 'confirmar':
                    if ($pedido->estado != 'pendiente') {
                        throw new \Exception('Solo se pueden confirmar pedidos pendientes.');
                    }
                    $pedido->estado = 'confirmado';
                    $pedido->fecha_confirmacion = now();
                    $mensaje = 'Pedido confirmado correctamente.';
                    break;
                    
                case 'enviar':
                    if ($pedido->estado != 'confirmado') {
                        throw new \Exception('Solo se pueden enviar pedidos confirmados.');
                    }
                    $pedido->estado = 'enviado';
                    $mensaje = 'Pedido marcado como enviado.';
                    break;
                    
                case 'entregar':
                    if ($pedido->estado != 'enviado') {
                        throw new \Exception('Solo se pueden entregar pedidos enviados.');
                    }
                    $pedido->estado = 'entregado';
                    $pedido->fecha_entrega = now();
                    $mensaje = 'Pedido marcado como entregado.';
                    break;
                    
                case 'cancelar':
                    if ($pedido->estado == 'cancelado') {
                        throw new \Exception('El pedido ya está cancelado.');
                    }
                    if ($pedido->estado == 'entregado') {
                        throw new \Exception('No se puede cancelar un pedido entregado.');
                    }
                    $pedido->estado = 'cancelado';
                    $mensaje = 'Pedido cancelado correctamente.';
                    break;
                    
                default:
                    throw new \Exception('Acción no válida.');
            }
            
            $pedido->save();
            
            // Registrar en historial
            PedidoHistorial::create([
                'pedido_id' => $id,
                'usuario_id' => $this->usuarioId,
                'accion' => 'cambio_estado',
                'detalles' => "Estado cambiado de '$estado_anterior' a '{$pedido->estado}' mediante acción rápida. Usuario: {$this->usuarioNombre}",
                'fecha' => now()
            ]);
            
            // Registrar como responsable si no existe
            $existeResponsable = PedidoResponsable::where('pedido_id', $id)
                ->where('usuario_id', $this->usuarioId)
                ->exists();
                
            if (!$existeResponsable) {
                PedidoResponsable::create([
                    'pedido_id' => $id,
                    'usuario_id' => $this->usuarioId,
                    'fecha_asignacion' => now()
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('vendedor.pedidos.ver', $id)
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Éxito!',
                    'message' => $mensaje
                ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar acción: ' . $e->getMessage());
            
            return redirect()->route('vendedor.pedidos.ver', $id)
                ->with('swal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => $e->getMessage()
                ]);
        }
    }
}