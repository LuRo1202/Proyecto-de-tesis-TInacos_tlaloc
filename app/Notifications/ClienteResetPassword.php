<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ClienteResetPassword extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('cliente.reset.form', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Tomamos las constantes de tu archivo de configuración
        $empresaNombre = defined('EMPRESA_NOMBRE') ? EMPRESA_NOMBRE : 'Tanques Tláloc';
        $empresaDireccion = defined('EMPRESA_DIRECCION') ? EMPRESA_DIRECCION : 'Av Morelos Oriente 186 a, Colonia San Cristobal Centro, Ecatepec de Morelos, Estado de México';
        $empresaTelefono = defined('EMPRESA_TELEFONO') ? EMPRESA_TELEFONO : '+52 55 4017 5803';
        $correoFrom = defined('CORREO_FROM') ? CORREO_FROM : 'tanquestlaloc@outlook.com';
        $colorPrimario = defined('COLOR_PRIMARIO') ? COLOR_PRIMARIO : '#7fad39';
        $colorSecundario = defined('COLOR_SECUNDARIO') ? COLOR_SECUNDARIO : '#5a8a20';

        // Crear el HTML completo del correo
        $html = $this->plantillaCompleta($url, $notifiable->nombre, [
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

    private function plantillaCompleta($url, $nombreUsuario, $data)
    {
        $verdePrimario = $data['color_primario'];
        $verdeOscuro = $data['color_secundario'];
        
        return '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Recuperar Contraseña - Tanques Tláloc</title>
            <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@800&family=Lato:wght@400;700&display=swap" rel="stylesheet">
            <style>
                @media only screen and (max-width: 600px) {
                    .container { width: 100% !important; border-radius: 0 !important; }
                    .header { padding: 45px 20px !important; }
                    .content { padding: 30px 20px !important; }
                }
                body {
                    margin: 0;
                    padding: 0;
                    background-color: #f0f4f8;
                    font-family: "Lato", Arial, sans-serif;
                }
                .btn {
                    display: inline-block;
                    background: linear-gradient(135deg, ' . $verdePrimario . ' 0%, ' . $verdeOscuro . ' 100%);
                    color: #ffffff;
                    text-decoration: none;
                    padding: 14px 35px;
                    border-radius: 50px;
                    font-weight: 600;
                    font-size: 16px;
                    box-shadow: 0 4px 15px rgba(127, 173, 57, 0.3);
                    transition: all 0.3s ease;
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(127, 173, 57, 0.4);
                }
            </style>
        </head>
        <body>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center" style="padding: 20px 0;">
                        <table class="container" width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.12); border: 1px solid #e2e8f0;">
                            
                            <!-- HEADER -->
                            <tr>
                                <td class="header" style="background: linear-gradient(135deg, ' . $verdePrimario . ' 0%, ' . $verdeOscuro . ' 100%); padding: 50px 40px; text-align: center;">
                                    <div style="margin-bottom: 12px;">
                                        <span style="background: rgba(255,255,255,0.15); color: #ffffff; padding: 6px 16px; border-radius: 50px; font-size: 10px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.3);">
                                            Recuperación de Contraseña
                                        </span>
                                    </div>
                                    <h1 style="color: #ffffff; margin: 0; font-family: \'Raleway\', sans-serif; font-size: 38px; font-weight: 800; letter-spacing: -1px; line-height: 1.2;">
                                        TANQUES TLALOC
                                    </h1>
                                    <p style="color: #ffffff; margin: 12px 0 0; font-size: 14px; letter-spacing: 4px; opacity: 0.9; text-transform: uppercase; font-weight: 300;">
                                        Creadores del Tinaco Bala
                                    </p>
                                    <div style="height: 3px; width: 40px; background-color: #ffffff; margin: 25px auto 0; border-radius: 10px; opacity: 0.5;"></div>
                                </td>
                            </tr>

                            <!-- CONTENIDO PRINCIPAL -->
                            <tr>
                                <td class="content" style="padding: 50px 45px; background-color: #ffffff;">
                                    <div style="color: #2D3748; line-height: 1.8; font-size: 16px; text-align: left;">
                                        <h2 style="color: #2D3748; font-size: 24px; margin-bottom: 20px; font-weight: 700;">
                                            ¡Hola ' . $nombreUsuario . '!
                                        </h2>
                                        
                                        <p style="color: #4A5568; font-size: 16px; margin-bottom: 25px; line-height: 1.6;">
                                            Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong style="color: ' . $verdePrimario . ';">Tanques Tláloc</strong>.
                                        </p>
                                        
                                        <div style="background-color: #f8f9fa; border-left: 4px solid ' . $verdePrimario . '; padding: 20px; margin: 30px 0; border-radius: 8px;">
                                            <p style="margin: 0 0 15px 0; color: #2D3748; font-size: 15px;">
                                                Para crear una nueva contraseña, haz clic en el siguiente botón:
                                            </p>
                                            <div style="text-align: center;">
                                                <a href="' . $url . '" class="btn" style="display: inline-block; background: linear-gradient(135deg, ' . $verdePrimario . ' 0%, ' . $verdeOscuro . ' 100%); color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(127, 173, 57, 0.3); transition: all 0.3s ease;">
                                                    🔑 Restablecer contraseña
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div style="background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 8px; padding: 15px; margin: 25px 0;">
                                            <p style="margin: 0; color: #856404; font-size: 14px;">
                                                <strong>⚠️ Importante:</strong> Este enlace expirará en <strong>60 minutos</strong>.
                                            </p>
                                        </div>
                                        
                                        <p style="color: #718096; font-size: 14px; margin: 20px 0 10px; font-style: italic;">
                                            Si no solicitaste este cambio, puedes ignorar este mensaje y tu contraseña seguirá siendo la misma.
                                        </p>
                                        
                                        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
                                        
                                        <p style="color: #4A5568; font-size: 15px; margin-bottom: 5px;">
                                            Saludos,
                                        </p>
                                        <p style="color: ' . $verdePrimario . '; font-size: 16px; font-weight: 600; margin-top: 0;">
                                            Equipo de Tanques Tláloc
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <!-- FOOTER -->
                            <tr>
                                <td style="background-color: #0f172a; padding: 45px; border-top: 6px solid ' . $verdePrimario . ';">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="text-align: center; padding-bottom: 30px;">
                                                <h4 style="color: #ffffff; margin: 0; font-family: \'Raleway\', sans-serif; font-size: 18px; text-transform: uppercase; letter-spacing: 2px;">
                                                    ' . $data['nombre'] . '
                                                </h4>
                                                <p style="color: #94a3b8; font-size: 13px; margin: 10px 0;">Especialistas en Rotomoldeo con más de 20 años de experiencia.</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 1px solid #334155; padding-top: 30px;">
                                                    <tr>
                                                        <td width="50%" valign="top">
                                                            <p style="color: ' . $verdePrimario . '; font-size: 11px; font-weight: bold; margin: 0 0 8px; text-transform: uppercase;">📍 Ubicación</p>
                                                            <p style="color: #e2e8f0; font-size: 13px; margin: 0; line-height: 1.5;">' . $data['direccion'] . '</p>
                                                        </td>
                                                        <td width="50%" valign="top">
                                                            <p style="color: ' . $verdePrimario . '; font-size: 11px; font-weight: bold; margin: 0 0 8px; text-transform: uppercase;">📞 Contacto</p>
                                                            <p style="color: #e2e8f0; font-size: 13px; margin: 0; line-height: 1.5;">
                                                                Tel: ' . $data['telefono'] . '<br>
                                                                Email: ' . $data['email'] . '
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center; padding-top: 40px;">
                                                <p style="color: #475569; font-size: 10px; margin: 0; letter-spacing: 2px; text-transform: uppercase;">
                                                    © ' . date('Y') . ' TANQUES TLALOC | ESPECIALISTAS EN ROTOMOLDEO.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <p style="text-align: center; color: #94a3b8; font-size: 11px; margin-top: 25px;">
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