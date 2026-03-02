<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $tipo; // 'cliente' o 'usuario'

    public function __construct($token, $tipo = 'cliente')
    {
        $this->token = $token;
        $this->tipo = $tipo;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Determinar la ruta según el tipo
        if ($this->tipo === 'cliente') {
            $url = url(route('cliente.reset.form', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        } else {
            $url = url(route('usuario.reset.form', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        }

        // Tomamos las constantes de tu archivo de configuración
        $empresaNombre = defined('EMPRESA_NOMBRE') ? EMPRESA_NOMBRE : 'Tanques Tláloc';
        $empresaDireccion = defined('EMPRESA_DIRECCION') ? EMPRESA_DIRECCION : 'Av Morelos Oriente 186 a, Colonia San Cristobal Centro, Ecatepec de Morelos, Estado de México';
        $empresaTelefono = defined('EMPRESA_TELEFONO') ? EMPRESA_TELEFONO : '+52 55 4017 5803';
        $correoFrom = defined('CORREO_FROM') ? CORREO_FROM : 'tanquestlaloc@outlook.com';
        $colorPrimario = defined('COLOR_PRIMARIO') ? COLOR_PRIMARIO : '#7fad39';
        $colorSecundario = defined('COLOR_SECUNDARIO') ? COLOR_SECUNDARIO : '#5a8a20';

        // 👈 CORREGIDO: Pasamos $this->tipo en lugar de $tipoTexto
        $html = $this->plantillaCompleta($url, $notifiable->nombre, $this->tipo, [
            'nombre' => $empresaNombre,
            'direccion' => $empresaDireccion,
            'telefono' => $empresaTelefono,
            'email' => $correoFrom,
            'color_primario' => $colorPrimario,
            'color_secundario' => $colorSecundario
        ]);

        return (new MailMessage)
            ->subject('🔐 Recuperar contraseña - Tanques Tláloc')
            ->view('emails.plantilla', ['htmlContent' => $html]);
    }

    private function plantillaCompleta($url, $nombreUsuario, $tipo, $data)
{
    $verdePrimario = $data['color_primario'];
    $verdeOscuro = $data['color_secundario'];
    
    $mensaje = ($tipo === 'cliente') 
        ? 'Recibimos una solicitud para restablecer la contraseña de tu cuenta de cliente.'
        : 'Recibimos una solicitud para restablecer la contraseña de tu cuenta administrativa.';
    
    return '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperar Contraseña - Tanques Tláloc</title>
    </head>
    <body style="margin:0; padding:0; background-color:#f0f4f8; font-family: Arial, Helvetica, sans-serif;">
        
        <!-- TABLA PRINCIPAL: centrado y ancho fijo -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f0f4f8" style="background-color:#f0f4f8;">
            <tr>
                <td align="center" style="padding:20px 10px;">
                    
                    <!-- CONTENEDOR PRINCIPAL -->
                    <table width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="width:600px; max-width:600px; background-color:#ffffff; border-radius:16px; border:1px solid #e2e8f0; border-collapse:separate; overflow:hidden;">
                        
                        <!-- HEADER con color sólido (Outlook no soporta gradientes) -->
                        <tr>
                            <td bgcolor="' . $verdePrimario . '" style="background-color:' . $verdePrimario . '; padding:40px 30px 30px 30px; text-align:center;">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            <div style="margin-bottom:12px;">
                                                <span style="background:rgba(255,255,255,0.15); color:#ffffff; padding:6px 16px; border-radius:50px; font-size:10px; font-weight:bold; letter-spacing:2px; text-transform:uppercase; border:1px solid rgba(255,255,255,0.3); display:inline-block;">
                                                    RECUPERACIÓN DE CONTRASEÑA
                                                </span>
                                            </div>
                                            <h1 style="color:#ffffff; margin:0; font-size:38px; font-weight:bold; letter-spacing:-1px; line-height:1.2;">
                                                TANQUES TLALOC
                                            </h1>
                                            <p style="color:#ffffff; margin:12px 0 0; font-size:14px; letter-spacing:4px; opacity:0.9; text-transform:uppercase;">
                                                Creadores del Tinaco Bala
                                            </p>
                                            <div style="height:3px; width:40px; background-color:#ffffff; margin:25px auto 0; border-radius:10px;"></div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- CONTENIDO -->
                        <tr>
                            <td bgcolor="#ffffff" style="background-color:#ffffff; padding:40px 30px;">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="color:#2D3748; font-size:16px; line-height:1.8;">
                                            <h2 style="color:#2D3748; font-size:24px; margin:0 0 20px 0; font-weight:bold;">
                                                ¡Hola ' . $nombreUsuario . '!
                                            </h2>
                                            
                                            <p style="margin:0 0 25px 0;">
                                                ' . $mensaje . '
                                            </p>
                                            
                                            <!-- CAJA DE BOTÓN (con bordes) -->
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f8f9fa" style="background-color:#f8f9fa; border-left:4px solid ' . $verdePrimario . '; border-radius:8px; margin:30px 0;">
                                                <tr>
                                                    <td style="padding:20px; text-align:center;">
                                                        <p style="margin:0 0 15px 0; color:#2D3748; font-size:15px;">
                                                            Para crear una nueva contraseña, haz clic en el siguiente botón:
                                                        </p>
                                                        <!-- BOTÓN con color sólido (Outlook no soporta gradientes) -->
                                                        <table border="0" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                                            <tr>
                                                                <td align="center" bgcolor="' . $verdePrimario . '" style="background-color:' . $verdePrimario . '; border-radius:50px;">
                                                                    <a href="' . $url . '" style="display:inline-block; background-color:' . $verdePrimario . '; color:#ffffff; text-decoration:none; padding:14px 35px; border-radius:50px; font-weight:600; font-size:16px;">🔑 Restablecer contraseña</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            
                                            <!-- AVISO IMPORTANTE -->
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#fff3cd" style="background-color:#fff3cd; border:1px solid #ffeeba; border-radius:8px; margin:25px 0;">
                                                <tr>
                                                    <td style="padding:15px;">
                                                        <p style="margin:0; color:#856404; font-size:14px;">
                                                            <strong>⚠️ Importante:</strong> Este enlace expirará en <strong>60 minutos</strong>.
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                            
                                            <p style="color:#718096; font-size:14px; margin:20px 0 10px; font-style:italic;">
                                                Si no solicitaste este cambio, puedes ignorar este mensaje.
                                            </p>
                                            
                                            <hr style="border:none; border-top:1px solid #e2e8f0; margin:30px 0;">
                                            
                                            <p style="color:#4A5568; font-size:15px; margin:0 0 5px 0;">
                                                Saludos,
                                            </p>
                                            <p style="color:' . $verdePrimario . '; font-size:16px; font-weight:600; margin:0;">
                                                Equipo de Tanques Tláloc
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- FOOTER -->
                        <tr>
                            <td bgcolor="#0f172a" style="background-color:#0f172a; padding:30px; border-top:6px solid ' . $verdePrimario . ';">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center" style="padding-bottom:20px;">
                                            <h4 style="color:#ffffff; margin:0; font-size:18px; text-transform:uppercase; letter-spacing:2px;">
                                                ' . $data['nombre'] . '
                                            </h4>
                                            <p style="color:#94a3b8; font-size:13px; margin:10px 0 0 0;">
                                                Especialistas en Rotomoldeo
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td width="50%" valign="top" style="padding-right:10px;">
                                                        <p style="color:' . $verdePrimario . '; font-size:11px; font-weight:bold; margin:0 0 5px 0; text-transform:uppercase;">📍 Ubicación</p>
                                                        <p style="color:#e2e8f0; font-size:12px; margin:0; line-height:1.5;">' . $data['direccion'] . '</p>
                                                    </td>
                                                    <td width="50%" valign="top">
                                                        <p style="color:' . $verdePrimario . '; font-size:11px; font-weight:bold; margin:0 0 5px 0; text-transform:uppercase;">📞 Contacto</p>
                                                        <p style="color:#e2e8f0; font-size:12px; margin:0; line-height:1.5;">
                                                            Tel: ' . $data['telefono'] . '<br>
                                                            Email: ' . $data['email'] . '
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="padding-top:20px;">
                                            <p style="color:#475569; font-size:9px; margin:0; text-transform:uppercase;">
                                                © ' . date('Y') . ' TANQUES TLALOC | ESPECIALISTAS EN ROTOMOLDEO.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    
                    <p style="text-align:center; color:#94a3b8; font-size:11px; margin-top:20px;">
                        Este es un mensaje automático del sistema de recuperación de contraseña de Tanques Tlaloc.
                    </p>
                </td>
            </tr>
        </table>
        
    </body>
    </html>
    ';
}
}