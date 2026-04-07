<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pedido;

class PedidoStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $estadoAnterior;
    public $estadoNuevo;

    public function __construct(Pedido $pedido, $estadoAnterior, $estadoNuevo)
    {
        $this->pedido = $pedido;
        $this->estadoAnterior = $estadoAnterior;
        $this->estadoNuevo = $estadoNuevo;
    }

    public function build()
    {
        // ✅ CORREGIDO: NO usar PlantillaBase, devolver directamente el HTML
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($this->getSubject())
                    ->html($this->generarContenidoPedido());  // ← Directamente el HTML
    }

    private function getSubject()
    {
        $estadosTexto = [
            'pendiente' => 'recibido',
            'confirmado' => 'confirmado',
            'enviado' => 'enviado',
            'entregado' => 'entregado',
            'cancelado' => 'cancelado'
        ];
        
        $texto = $estadosTexto[$this->estadoNuevo] ?? $this->estadoNuevo;
        
        return "Tu pedido #{$this->pedido->folio} ha sido {$texto} - Tanques Tláloc";
    }

    private function generarContenidoPedido()
    {
        $pedido = $this->pedido;
        $estadoNuevo = $this->estadoNuevo;
        $verde = '#7fad39';
        
        $coloresEstado = [
            'pendiente' => ['bg' => '#fff3cd', 'color' => '#856404', 'icono' => '🕐', 'texto' => 'Pendiente'],
            'confirmado' => ['bg' => '#d1ecf1', 'color' => '#0c5460', 'icono' => '✅', 'texto' => 'Confirmado'],
            'enviado' => ['bg' => '#cce5ff', 'color' => '#004085', 'icono' => '🚚', 'texto' => 'Enviado'],
            'entregado' => ['bg' => '#d4edda', 'color' => '#155724', 'icono' => '📦', 'texto' => 'Entregado'],
            'cancelado' => ['bg' => '#f8d7da', 'color' => '#721c24', 'icono' => '❌', 'texto' => 'Cancelado']
        ];
        
        $estadoInfo = $coloresEstado[$estadoNuevo] ?? $coloresEstado['pendiente'];
        
        $mensajes = [
            'confirmado' => "✅ ¡Excelente noticia! Tu pedido ha sido <strong>confirmado</strong>. En breve comenzaremos con la preparación.",
            'enviado' => "🚚 ¡Tu pedido está en camino! Nuestro equipo ya lo ha entregado a la paquetería.",
            'entregado' => "📦 ¡Tu pedido ha sido entregado! Esperamos que estés satisfecho con tu compra.",
            'cancelado' => "❌ Lamentamos informarte que tu pedido ha sido cancelado. Si tienes dudas, contáctanos.",
            'pendiente' => "📋 Hemos recibido tu pedido y está siendo revisado."
        ];
        
        $mensajePrincipal = $mensajes[$estadoNuevo] ?? "El estado de tu pedido ha sido actualizado a: <strong>" . ucfirst($estadoNuevo) . "</strong>";
        
        // Construir tabla de productos
        $productosHtml = '';
        foreach ($pedido->items as $item) {
            $productosHtml .= "
            <tr>
                <td style='padding: 12px; border-bottom: 1px solid #e2e8f0;'>
                    <strong>{$item->producto_nombre}</strong>
                </td>
                <td style='padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;'>
                    {$item->cantidad}
                </td>
                <td style='padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;'>
                    $" . number_format($item->precio, 2) . "
                </td>
                <td style='padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;'>
                    $" . number_format($item->cantidad * $item->precio, 2) . "
                </td>
            </tr>
            ";
        }
        
        $icono = $estadoInfo['icono'];
        $urlMisPedidos = route('cliente.pedidos');
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Actualización de tu pedido</title>
        </head>
        <body style='margin: 0; padding: 0; background-color: #f0f4f8; font-family: Arial, sans-serif;'>
            <table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color: #f0f4f8;'>
                <tr>
                    <td align='center' style='padding: 20px 0;'>
                        <table width='600' cellpadding='0' cellspacing='0' border='0' style='background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);'>
                            
                            <!-- HEADER -->
                            <tr>
                                <td style='background-color: $verde; padding: 40px 30px; text-align: center;'>
                                    <div style='color: #ffffff; font-size: 14px; letter-spacing: 2px; margin-bottom: 10px;'>PORTAL DE CONTACTO</div>
                                    <h1 style='color: #ffffff; margin: 0; font-size: 32px; font-weight: bold; letter-spacing: -1px;'>
                                        TANQUES TLÁLOC
                                    </h1>
                                    <p style='color: #ffffff; margin: 10px 0 0; font-size: 12px; letter-spacing: 4px;'>
                                        Creadores del Tinaco Bala
                                    </p>
                                    <div style='height: 3px; width: 40px; background-color: #ffffff; margin: 20px auto 0; border-radius: 3px;'></div>
                                </td>
                            </tr>
                            
                            <!-- CONTENIDO -->
                            <tr>
                                <td style='padding: 40px 35px; background-color: #ffffff;'>
                                    
                                    <div style='text-align: center; margin-bottom: 30px;'>
                                        <div style='background-color: #f0f9f0; border-radius: 50%; width: 70px; height: 70px; margin: 0 auto 15px auto; text-align: center; line-height: 70px; font-size: 36px;'>
                                            {$icono}
                                        </div>
                                        <h2 style='color: {$estadoInfo['color']}; margin: 0; font-size: 24px;'>
                                            ¡Pedido {$estadoInfo['texto']}!
                                        </h2>
                                    </div>
                                    
                                    <p style='color: #64748b; font-size: 16px; margin-bottom: 25px; line-height: 1.5;'>
                                        Hola <strong style='color: #1e293b;'>" . htmlspecialchars($pedido->cliente_nombre) . "</strong>,
                                    </p>
                                    
                                    <div style='background-color: {$estadoInfo['bg']}; padding: 20px; border-radius: 12px; margin: 20px 0; border-left: 4px solid {$estadoInfo['color']};'>
                                        <p style='margin: 0; color: {$estadoInfo['color']}; line-height: 1.6;'>
                                            {$mensajePrincipal}
                                        </p>
                                    </div>
                                    
                                    <div style='background-color: #f8fafc; padding: 25px; border-radius: 12px; margin: 25px 0;'>
                                        <h3 style='margin: 0 0 20px 0; color: #1e293b; font-size: 18px;'>
                                            📋 Resumen de tu Pedido
                                        </h3>
                                        
                                        <table width='100%' cellpadding='0' cellspacing='0' border='0' style='width: 100%; border-collapse: collapse;'>
                                            <thead>
                                                <tr style='background-color: #f1f5f9;'>
                                                    <th style='padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0;'>Producto</th>
                                                    <th style='padding: 12px; text-align: center; border-bottom: 2px solid #e2e8f0;'>Cantidad</th>
                                                    <th style='padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0;'>Precio Unit.</th>
                                                    <th style='padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0;'>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {$productosHtml}
                                                <tr style='background-color: #f8fafc; font-weight: bold;'>
                                                    <td colspan='3' style='padding: 15px 12px; text-align: right; border-top: 2px solid #e2e8f0;'>
                                                        <strong>Total:</strong>
                                                    </td>
                                                    <td style='padding: 15px 12px; text-align: right; border-top: 2px solid #e2e8f0; color: $verde; font-size: 18px;'>
                                                        <strong>$" . number_format($pedido->total, 2) . "</strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div style='background-color: #f8fafc; padding: 20px; border-radius: 12px; margin: 25px 0; border-left: 4px solid $verde;'>
                                        <h4 style='margin: 0 0 15px 0; color: #1e293b; font-size: 16px;'>
                                            📍 Dirección de Entrega
                                        </h4>
                                        <p style='color: #334155; line-height: 1.6; margin: 0;'>
                                            " . nl2br(htmlspecialchars($pedido->cliente_direccion)) . "<br>
                                            " . htmlspecialchars($pedido->cliente_ciudad) . ", " . htmlspecialchars($pedido->cliente_estado) . "<br>
                                            CP: " . htmlspecialchars($pedido->codigo_postal) . "
                                        </p>
                                    </div>
                                    
                                    " . ($pedido->fecha_entrega && $estadoNuevo == 'enviado' ? "
                                    <div style='background-color: #ecfdf5; padding: 20px; border-radius: 12px; margin: 25px 0; border: 1px solid #a7f3d0;'>
                                        <h4 style='color: #065f46; margin: 0 0 10px 0; font-size: 16px;'>
                                            📅 Fecha estimada de entrega
                                        </h4>
                                        <p style='margin: 0; color: #065f46; font-size: 15px; font-weight: 500;'>
                                            " . \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') . "
                                        </p>
                                    </div>
                                    " : "") . "
                                    
                                    <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;'>
                                        <a href='{$urlMisPedidos}' 
                                           style='display: inline-block; background-color: $verde; color: white; padding: 12px 30px; 
                                                  text-decoration: none; border-radius: 6px; font-weight: 500;
                                                  margin: 0 5px 10px 5px;'>
                                             Ver mis pedidos
                                        </a>
                                        
                                        <a href='" . route('contacto') . "' 
                                           style='display: inline-block; background-color: #334155; color: white; padding: 12px 30px; 
                                                  text-decoration: none; border-radius: 6px; font-weight: 500;
                                                  margin: 0 5px 10px 5px;'>
                                             Contactar soporte
                                        </a>
                                    </div>
                                    
                                    <p style='color: #64748b; font-size: 12px; margin-top: 30px; text-align: center;'>
                                        ¿Tienes preguntas sobre tu pedido? Llámanos al <strong style='color: $verde;'>55 4017 5803</strong>
                                    </p>
                                    
                                </td>
                            </tr>
                            
                            <!-- FOOTER -->
                            <tr>
                                <td style='background-color: #0f172a; padding: 35px; border-top: 4px solid $verde;'>
                                    <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                        <tr>
                                            <td style='text-align: center; padding-bottom: 20px;'>
                                                <h4 style='color: #ffffff; margin: 0; font-size: 16px; letter-spacing: 2px;'>
                                                    TANQUES TLÁLOC
                                                </h4>
                                                <p style='color: #94a3b8; font-size: 12px; margin: 10px 0;'>
                                                    Especialistas en Rotomoldeo con más de 20 años de experiencia.
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='border-top: 1px solid #334155; padding-top: 20px;'>
                                                <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                                    <tr>
                                                        <td width='50%' valign='top' style='padding: 5px;'>
                                                            <p style='color: $verde; font-size: 11px; font-weight: bold; margin: 0 0 5px;'>📍 UBICACIÓN</p>
                                                            <p style='color: #e2e8f0; font-size: 12px; margin: 0; line-height: 1.5;'>
                                                                Av Morelos Oriente 186 a,<br>
                                                                Colonia San Cristobal Centro,<br>
                                                                Ecatepec, Estado de México
                                                            </p>
                                                        </td>
                                                        <td width='50%' valign='top' style='padding: 5px;'>
                                                            <p style='color: $verde; font-size: 11px; font-weight: bold; margin: 0 0 5px;'>📞 CONTACTO</p>
                                                            <p style='color: #e2e8f0; font-size: 12px; margin: 0; line-height: 1.5;'>
                                                                Tel: 55 4017 5803<br>
                                                                Email: " . config('mail.from.address') . "
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: center; padding-top: 25px;'>
                                                <p style='color: #475569; font-size: 10px; margin: 0; letter-spacing: 1px;'>
                                                    © " . date('Y') . " TANQUES TLÁLOC | ESPECIALISTAS EN ROTOMOLDEO.
                                                </p>
                                                <p style='color: #475569; font-size: 10px; margin: 5px 0 0;'>
                                                    Este es un mensaje automático del sistema de Tanques Tláloc.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
                        </table>
                    </td>
                </table>
            </table>
        </body>
        </html>
        ";
    }
}