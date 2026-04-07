<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\Templates\PlantillaBase;
use App\Models\Cliente;

class ClienteBienvenidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cliente;
    public $resetUrl;

    public function __construct(Cliente $cliente, $resetUrl)
    {
        $this->cliente = $cliente;
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        $contenidoInterno = $this->generarContenido();
        $contenidoCompleto = PlantillaBase::render($contenidoInterno);
        
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('🎉 Bienvenido a Tanques Tláloc - Activa tu cuenta')
                    ->html($contenidoCompleto);
    }

    private function generarContenido()
    {
        $verde = '#7fad39';
        
        return "
        <div style='text-align: center; margin-bottom: 30px;'>
            <div style='background-color: #f0f9f0; border-radius: 50%; width: 80px; height: 80px; margin: 0 auto 20px auto; display: flex; align-items: center; justify-content: center;'>
                <span style='font-size: 48px;'>🎉</span>
            </div>
            <h2 style='color: #7fad39; margin: 0;'>¡Bienvenido a Tanques Tláloc!</h2>
        </div>

        <p style='color: #64748b; font-size: 16px; margin-bottom: 25px;'>
            Hola <strong>" . htmlspecialchars($this->cliente->nombre) . "</strong>,
        </p>

        <p style='color: #334155; line-height: 1.6; margin-bottom: 20px;'>
            Un asesor de <strong>Tanques Tláloc</strong> te ha registrado en nuestra plataforma.
            Para comenzar a usar tu cuenta y dar seguimiento a tus pedidos, necesitas crear una contraseña.
        </p>

        <div style='background-color: #f8fafc; padding: 25px; border-radius: 12px; margin: 25px 0; border-left: 4px solid $verde; text-align: center;'>
            <p style='margin: 0 0 15px; color: #1e293b;'>
                <strong>Tu correo electrónico de acceso es:</strong><br>
                <span style='color: $verde;'>" . htmlspecialchars($this->cliente->email) . "</span>
            </p>
            
            <a href='" . $this->resetUrl . "' 
               style='display: inline-block; background-color: $verde; color: white; padding: 14px 28px; 
                      text-decoration: none; border-radius: 8px; font-weight: 600; margin: 15px 0;'>
                🔐 Crear mi contraseña
            </a>
            
            <p style='color: #64748b; font-size: 12px; margin: 15px 0 0;'>
                O copia este enlace en tu navegador:<br>
                <span style='color: $verde; word-break: break-all; font-size: 11px;'>" . $this->resetUrl . "</span>
            </p>
        </div>

        <div style='background-color: #f8fafc; padding: 20px; border-radius: 12px; margin: 25px 0;'>
            <h4 style='margin: 0 0 15px 0; color: #1e293b; font-size: 16px;'>
                <i class='fas fa-gift' style='margin-right: 8px; color: $verde;'></i>¿Qué puedes hacer?
            </h4>
            <ul style='color: #334155; line-height: 1.8; padding-left: 20px; margin: 0;'>
                <li>✅ Ver el historial de tus pedidos</li>
                <li>✅ Dar seguimiento a tus compras</li>
                <li>✅ Realizar pedidos en línea</li>
                <li>✅ Recibir notificaciones de tus pedidos</li>
            </ul>
        </div>

        <div style='background-color: #ecfdf5; padding: 20px; border-radius: 12px; margin: 25px 0; border: 1px solid #a7f3d0;'>
            <h4 style='color: #065f46; margin: 0 0 10px 0; font-size: 16px;'>
                💡 ¿Tienes dudas?
            </h4>
            <p style='margin: 0; color: #065f46;'>Contáctanos al <strong>55 4017 5803</strong></p>
        </div>

        <p style='color: #64748b; font-size: 12px; margin-top: 30px; text-align: center;'>
            Si no fuiste tú quien solicitó este registro, ignora este mensaje.
        </p>
        ";
    }
}