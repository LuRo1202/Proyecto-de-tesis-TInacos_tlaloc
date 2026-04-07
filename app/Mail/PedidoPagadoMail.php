<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\Templates\PlantillaBase;
use App\Models\Pedido;

class PedidoPagadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $cliente;

    public function __construct(Pedido $pedido, $cliente)
    {
        $this->pedido = $pedido;
        $this->cliente = $cliente;
    }

    public function build()
    {
        $contenidoInterno = $this->generarContenido();
        $contenidoCompleto = PlantillaBase::render($contenidoInterno);
        
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('✅ Pago confirmado - Tu pedido está en proceso - Tanques Tláloc')
                    ->html($contenidoCompleto);
    }

    private function generarContenido()
    {
        $verde = '#7fad39';
        $pedido = $this->pedido;
        $cliente = $this->cliente;
        
        return "
        <div style='text-align: center; margin-bottom: 30px;'>
            <div style='background-color: #e8f5e9; border-radius: 50%; width: 80px; height: 80px; margin: 0 auto 20px auto; display: flex; align-items: center; justify-content: center;'>
                <span style='font-size: 48px;'>✅</span>
            </div>
            <h2 style='color: $verde; margin: 0;'>¡Pago Confirmado!</h2>
        </div>

        <p style='color: #64748b; font-size: 16px; margin-bottom: 25px;'>
            Hola <strong>" . htmlspecialchars($cliente->nombre) . "</strong>,
        </p>

        <p style='color: #334155; line-height: 1.6; margin-bottom: 20px;'>
            Hemos recibido tu pago correctamente. Tu pedido <strong>#" . $pedido->folio . "</strong> ya está en proceso.
        </p>

        <div style='background-color: #f8fafc; padding: 25px; border-radius: 12px; margin: 25px 0; border-left: 4px solid $verde;'>
            <h4 style='margin: 0 0 15px 0; color: #1e293b; font-size: 16px;'>Detalles del pedido:</h4>
            <p style='margin: 5px 0;'><strong>📄 Folio:</strong> {$pedido->folio}</p>
            <p style='margin: 5px 0;'><strong>💰 Total pagado:</strong> $" . number_format($pedido->total, 2) . "</p>
            <p style='margin: 5px 0;'><strong>📅 Fecha:</strong> " . now()->format('d/m/Y H:i') . "</p>
            <p style='margin: 5px 0;'><strong>🚚 Estado:</strong> Pendiente de envío</p>
        </div>

        <p style='color: #334155; line-height: 1.6; margin-bottom: 20px;'>
            Te notificaremos cuando tu pedido sea enviado.
        </p>

        <div style='text-align: center; margin: 30px 0;'>
            <a href='" . route('cliente.pedido.ver', $pedido->id) . "' 
               style='display: inline-block; background-color: $verde; color: white; padding: 14px 28px; 
                      text-decoration: none; border-radius: 8px; font-weight: 600;'>
                Ver mi pedido
            </a>
        </div>

        <p style='color: #64748b; font-size: 12px; margin-top: 30px; text-align: center;'>
            ¿Tienes dudas? Contáctanos al <strong>55 4017 5803</strong>
        </p>
        ";
    }
}