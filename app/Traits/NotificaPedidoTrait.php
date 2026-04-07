<?php

namespace App\Traits;

use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Mail\PedidoStatusMail;
use App\Mail\PedidoPagoConfirmadoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

trait NotificaPedidoTrait
{
    /**
     * Envía notificación por email al cliente sobre cambio de estado
     */
    protected function enviarNotificacionEstado(Pedido $pedido, $estadoAnterior, $estadoNuevo)
    {
        // Obtener email desde la relación cliente()
        $clienteEmail = $pedido->cliente ? $pedido->cliente->email : null;
        
        // No enviar si no hay email del cliente
        if (empty($clienteEmail)) {
            Log::info("No se pudo enviar notificación - Pedido {$pedido->folio} no tiene cliente asociado o el cliente no tiene email");
            return false;
        }
        
        // Estados que merecen notificación
        $estadosNotificables = ['confirmado', 'enviado', 'entregado', 'cancelado'];
        
        if (!in_array($estadoNuevo, $estadosNotificables)) {
            Log::info("No se envía notificación para estado: {$estadoNuevo}");
            return false;
        }
        
        // Evitar notificaciones duplicadas (mismo estado)
        $ultimaNotificacion = PedidoHistorial::where('pedido_id', $pedido->id)
            ->where('accion', 'notificacion_enviada')
            ->where('detalles', 'LIKE', "%estado: {$estadoNuevo}%")
            ->latest()
            ->first();
        
        if ($ultimaNotificacion) {
            Log::info("Notificación ya enviada para pedido {$pedido->folio} - estado: {$estadoNuevo}");
            return false;
        }
        
        try {
            Mail::to($clienteEmail)->send(new PedidoStatusMail($pedido, $estadoAnterior, $estadoNuevo));
            
            // Registrar en historial
            PedidoHistorial::create([
                'pedido_id' => $pedido->id,
                'usuario_id' => auth()->check() ? auth()->id() : 1,
                'accion' => 'notificacion_enviada',
                'detalles' => "Email enviado al cliente ({$clienteEmail}) - estado: {$estadoNuevo}",
                'fecha' => now()
            ]);
            
            Log::info("Notificación enviada para pedido {$pedido->folio} - Estado: {$estadoNuevo} - Email: {$clienteEmail}");
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación para pedido {$pedido->folio}: " . $e->getMessage());
            
            PedidoHistorial::create([
                'pedido_id' => $pedido->id,
                'usuario_id' => auth()->check() ? auth()->id() : 1,
                'accion' => 'error_notificacion',
                'detalles' => "Error al enviar email: " . substr($e->getMessage(), 0, 200),
                'fecha' => now()
            ]);
            
            return false;
        }
    }
    
    /**
     * Envía notificación de confirmación de pago
     */
    protected function enviarNotificacionPagoConfirmado(Pedido $pedido)
    {
        // Obtener email desde la relación cliente()
        $clienteEmail = $pedido->cliente ? $pedido->cliente->email : null;
        
        if (empty($clienteEmail)) {
            Log::info("No se pudo enviar notificación de pago - Pedido {$pedido->folio} no tiene cliente asociado");
            return false;
        }
        
        try {
            Mail::to($clienteEmail)->send(new PedidoPagoConfirmadoMail($pedido));
            
            PedidoHistorial::create([
                'pedido_id' => $pedido->id,
                'usuario_id' => auth()->check() ? auth()->id() : 1,
                'accion' => 'notificacion_pago',
                'detalles' => "Email de pago confirmado enviado a {$clienteEmail}",
                'fecha' => now()
            ]);
            
            Log::info("Notificación de pago enviada para pedido {$pedido->folio} - Email: {$clienteEmail}");
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación de pago: " . $e->getMessage());
            return false;
        }
    }
}