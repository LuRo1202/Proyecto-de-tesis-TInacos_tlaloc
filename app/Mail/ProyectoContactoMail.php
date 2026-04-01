<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\Templates\PlantillaBase;

class ProyectoContactoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $datos;
    public $archivoPath;
    public $tipo;
    public $archivoInfo;

    public function __construct($datos, $archivoPath = null, $archivoInfo = null, $tipo = 'admin')
    {
        $this->datos = $datos;
        $this->archivoPath = $archivoPath;
        $this->archivoInfo = $archivoInfo;
        $this->tipo = $tipo;
    }

    public function build()
    {
        if ($this->tipo === 'admin') {
            $contenidoInterno = $this->generarContenidoProyectoAdmin($this->datos, $this->archivoInfo);
            $contenidoCompleto = PlantillaBase::render($contenidoInterno);
            
            $mail = $this->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject('Nueva Solicitud de Proyecto - ' . $this->datos['nombre'])
                        ->html($contenidoCompleto);

            if ($this->archivoPath && file_exists($this->archivoPath)) {
                $mail->attach($this->archivoPath);
            }

            return $mail;
        } else {
            $contenidoInterno = $this->generarContenidoProyectoCliente($this->datos);
            $contenidoCompleto = PlantillaBase::render($contenidoInterno);
            
            return $this->from(config('mail.from.address'), config('mail.from.name'))
                        ->subject('Hemos recibido tu solicitud de proyecto - Tanques Tlaloc')
                        ->html($contenidoCompleto);
        }
    }

    /**
     * TU DISEÑO ORIGINAL - Contenido para ADMIN (proyecto)
     */
    private function generarContenidoProyectoAdmin($datos, $archivo_info = null)
    {
        $nombre = htmlspecialchars($datos['nombre']);
        $email = htmlspecialchars($datos['email']);
        $telefono = htmlspecialchars($datos['telefono']);
        $comentarios = htmlspecialchars($datos['comentarios']);
        $verde = '#7fad39';
        
        $formatBytes = function($bytes, $precision = 2) {
            $units = ['B', 'KB', 'MB', 'GB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= (1 << (10 * $pow));
            return round($bytes, $precision) . ' ' . $units[$pow];
        };
        
        return "
        <h2 style='color: $verde; margin-top: 0;'>NUEVA SOLICITUD DE PROYECTO</h2>
        <p style='color: #64748b; margin-bottom: 25px; font-size: 14px;'>
            Un cliente ha enviado una solicitud de proyecto a través del formulario web.
        </p>

        <div style='background-color: #f8fafc; padding: 25px; border-radius: 12px; margin: 25px 0; border-left: 4px solid $verde;'>
            <h3 style='margin: 0 0 20px 0; color: #1e293b; font-size: 18px;'>
                <i class='fas fa-user-circle' style='margin-right: 8px;'></i>Información del Cliente
            </h3>
            
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='padding: 12px 0; border-bottom: 1px solid #e2e8f0; width: 30%;'>
                        <strong style='color: #475569;'>Nombre:</strong>
                    </td>
                    <td style='padding: 12px 0; border-bottom: 1px solid #e2e8f0;'>
                        <span style='color: #1e293b; font-weight: 500;'>$nombre</span>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 12px 0; border-bottom: 1px solid #e2e8f0;'>
                        <strong style='color: #475569;'>Teléfono:</strong>
                    </td>
                    <td style='padding: 12px 0; border-bottom: 1px solid #e2e8f0;'>
                        <a href='tel:$telefono' style='color: $verde; text-decoration: none; font-weight: 500;'>
                            $telefono
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 12px 0; border-bottom: 1px solid #e2e8f0;'>
                        <strong style='color: #475569;'>Correo Electrónico:</strong>
                    </td>
                    <td style='padding: 12px 0; border-bottom: 1px solid #e2e8f0;'>
                        <a href='mailto:$email' style='color: $verde; text-decoration: none; font-weight: 500;'>
                            $email
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 12px 0; border-bottom: 1px solid #e2e8f0;'>
                        <strong style='color: #475569;'>Archivo Adjunto:</strong>
                    </td>
                    <td style='padding: 12px 0; border-bottom: 1px solid #e2e8f0;'>
                        " . 
                        ($archivo_info ? 
                        "<div style='background-color: #f0f9ff; padding: 12px; border-radius: 8px; border: 1px solid #bae6fd;'>
                            <div style='display: flex; align-items: center;'>
                                <div style='background-color: $verde; color: white; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;'>
                                    <i class='fas fa-paperclip'></i>
                                </div>
                                <div>
                                    <div style='font-weight: 600; color: #1e293b;'>" . htmlspecialchars($archivo_info['nombre']) . "</div>
                                    <div style='font-size: 12px; color: #64748b;'>
                                        <span style='margin-right: 12px;'>" . $formatBytes(filesize($archivo_info['ruta'])) . "</span>
                                        <span>" . strtoupper(pathinfo($archivo_info['nombre'], PATHINFO_EXTENSION)) . "</span>
                                    </div>
                                </div>
                            </div>
                        </div>"
                        : 
                        "<span style='color: #94a3b8; font-style: italic;'>No se adjuntó archivo</span>") . 
                        "
                    </td>
                </tr>
            </table>
        </div>

        <div style='background-color: #fff8e1; padding: 25px; border-radius: 12px; margin: 25px 0; border-left: 4px solid #ffc107;'>
            <h3 style='margin: 0 0 20px 0; color: #1e293b; font-size: 18px;'>
                <i class='fas fa-lightbulb' style='margin-right: 8px; color: #ffc107;'></i>Descripción del Proyecto
            </h3>
            
            <div style='background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;'>
                <p style='margin: 0; color: #334155; line-height: 1.8; font-size: 15px;'>
                    " . nl2br($comentarios) . "
                </p>
            </div>
        </div>

        <div style='background-color: #ecfdf5; padding: 20px; border-radius: 10px; margin: 25px 0; border: 1px solid #a7f3d0;'>
            <h4 style='color: #065f46; margin: 0 0 15px 0; font-size: 16px;'>
                <i class='fas fa-clock' style='margin-right: 8px;'></i>Información de la Solicitud
            </h4>
            <table style='width: 100%;'>
                <tr>
                    <td style='padding: 8px 0;'>
                        <strong style='color: #475569;'>Fecha y Hora:</strong>
                        " . date('d/m/Y H:i:s') . "
                    </td>
                </tr>
                <tr>
                    <td style='padding: 8px 0;'>
                        <strong style='color: #475569;'>Tipo de Solicitud:</strong>
                        Proyecto Especial / Cotización Personalizada
                    </td>
                </tr>
            </table>
        </div>

        <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;'>
            <a href='mailto:$email' 
               style='display: inline-block; background-color: $verde; color: white; padding: 12px 30px; 
                      text-decoration: none; border-radius: 6px; font-weight: 500; margin-right: 10px;'>
                <i class='fas fa-reply' style='margin-right: 8px;'></i>Responder al Cliente
            </a>
            
            <a href='tel:$telefono' 
               style='display: inline-block; background-color: #10b981; color: white; padding: 12px 30px; 
                      text-decoration: none; border-radius: 6px; font-weight: 500;'>
                <i class='fas fa-phone' style='margin-right: 8px;'></i>Llamar al Cliente
            </a>
        </div>

        " . ($archivo_info ? 
        "<div style='margin-top: 20px; text-align: center;'>
            <p style='color: #64748b; font-size: 13px;'>
                <i class='fas fa-paperclip'></i> Archivo adjunto incluido en este correo: " . htmlspecialchars($archivo_info['nombre']) . "
            </p>
        </div>" 
        : "");
    }

    /**
     * TU DISEÑO ORIGINAL - Contenido para CLIENTE (proyecto)
     */
    private function generarContenidoProyectoCliente($datos)
    {
        $nombre = htmlspecialchars($datos['nombre']);
        $email = htmlspecialchars($datos['email']);
        $telefono = htmlspecialchars($datos['telefono']);
        $verde = '#7fad39';
        
        return "
        <div style='text-align: center; margin-bottom: 30px;'>
            <div style='background-color: #f0f9f0; border-radius: 50%; width: 80px; height: 80px; margin: 0 auto 20px auto; display: flex; align-items: center; justify-content: center; text-align: center;'>
                <span style='font-size: 48px; line-height: 80px; color: #7fad39; display: inline-block; width: 100%; height: 100%; vertical-align: middle;'>✓</span>
            </div>
            <h2 style='color: #7fad39; margin: 0;'>¡Solicitud de Proyecto Recibida!</h2>
        </div>

        <p style='color: #64748b; font-size: 16px; margin-bottom: 25px;'>
            Hola <strong style='color: #1e293b;'>$nombre</strong>,
        </p>

        <p style='color: #334155; line-height: 1.6; margin-bottom: 20px;'>
            Hemos recibido tu solicitud de proyecto correctamente. Uno de nuestros especialistas revisará la información que nos proporcionaste.
        </p>

        <div style='background-color: #f8fafc; padding: 20px; border-radius: 12px; margin: 25px 0; border-left: 4px solid $verde;'>
            <h4 style='margin: 0 0 15px 0; color: #1e293b; font-size: 16px;'>
                <i class='fas fa-clock' style='margin-right: 8px;'></i>Próximos pasos:
            </h4>
            <ul style='color: #334155; line-height: 1.8; padding-left: 20px;'>
                <li>Un asesor especializado analizará los detalles de tu proyecto</li>
                <li>Te contactaremos en menos de 48 horas hábiles</li>
                <li>Podemos solicitar información adicional para cotizar con precisión</li>
                <li>Responderemos a este correo: <strong>$email</strong> o al teléfono <strong>$telefono</strong></li>
            </ul>
        </div>

        <div style='background-color: #f8fafc; padding: 20px; border-radius: 12px; margin: 25px 0;'>
            <h4 style='margin: 0 0 15px 0; color: #1e293b; font-size: 16px;'>
                <i class='fas fa-lightbulb' style='margin-right: 8px;'></i>¿Qué sigue?
            </h4>
            <p style='color: #334155; line-height: 1.6; margin: 0;'>
                Mientras tanto, puedes seguir explorando nuestro catálogo de productos o contactarnos directamente si tienes alguna duda.
            </p>
        </div>

        <p style='color: #64748b; font-size: 14px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;'>
            Si no fuiste tú quien solicitó esta información, por favor ignora este mensaje o contáctanos para reportarlo.
        </p>
        ";
    }
}