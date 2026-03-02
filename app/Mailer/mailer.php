<?php
namespace App\Mailer;

require_once __DIR__ . '/config.php';

$phpmailerPath = __DIR__ . '/PHPMailer/src/';

if (!file_exists($phpmailerPath . 'PHPMailer.php')) {
    error_log("ERROR: PHPMailer no encontrado en: $phpmailerPath");
}

require_once $phpmailerPath . 'Exception.php';
require_once $phpmailerPath . 'PHPMailer.php';
require_once $phpmailerPath . 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    
    /**
     * Enviar correo al ADMIN (destinatario fijo)
     */
    public static function enviarCorreo($asunto, $contenidoHTML, $adjuntos = []) {
        
        $destinatario = MI_CORREO_PRINCIPAL;
        
        if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
            error_log("mail destino inválido: $destinatario");
            return ['success' => false, 'message' => 'Email destino inválido'];
        }
        
        try {
            $mail = new PHPMailer(true);
            
            // CONFIGURACIÓN SMTP
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port       = SMTP_PORT;
            
            // ✅ OPCIONES SSL - IMPORTANTE PARA QUE FUNCIONE
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // REMITENTE (quien envía)
            $mail->setFrom(SMTP_USER, CORREO_FROM_NAME);
            
            // DESTINATARIO (admin)
            $mail->addAddress($destinatario); 
            
            // REPLY-TO (para que las respuestas vayan a quien corresponde)
            $mail->addReplyTo(SMTP_USER, CORREO_FROM_NAME);
            
            // AGREGAR ADJUNTOS
            if (!empty($adjuntos) && is_array($adjuntos)) {
                foreach ($adjuntos as $adjunto) {
                    if (isset($adjunto['ruta']) && isset($adjunto['nombre']) && file_exists($adjunto['ruta'])) {
                        $mail->addAttachment($adjunto['ruta'], $adjunto['nombre']);
                        error_log("📎 Adjuntando archivo: " . $adjunto['nombre']);
                    }
                }
            }
            
            // CONTENIDO
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->Body    = self::plantillaCorreo($contenidoHTML);
            $mail->AltBody = strip_tags($contenidoHTML);
            
            // ENVIAR
            $mail->send();
            
            error_log("Correo enviado a: $destinatario" . (!empty($adjuntos) ? " con " . count($adjuntos) . " adjuntos" : ""));
            return ['success' => true, 'message' => 'Correo enviado'];
            
        } catch (Exception $e) {
            $error = "Error PHPMailer: " . $mail->ErrorInfo;
            error_log($error);
            return ['success' => false, 'message' => $error];
        }
    }
    
    /**
     * Enviar correo al CLIENTE (destinatario dinámico)
     */
    public static function enviarCorreoCliente($destinatario, $asunto, $contenidoHTML, $adjuntos = []) {
        
        if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
            error_log("email cliente inválido: $destinatario");
            return ['success' => false, 'message' => 'Email inválido'];
        }
        
        try {
            $mail = new PHPMailer(true);
            
            // CONFIGURACIÓN SMTP
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port       = SMTP_PORT;
            
            // ✅ OPCIONES SSL - IMPORTANTE PARA QUE FUNCIONE
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // REMITENTE (quien envía)
            $mail->setFrom(SMTP_USER, CORREO_FROM_NAME);
            
            // DESTINATARIO (cliente)
            $mail->addAddress($destinatario);
            
            // REPLY-TO (para que las respuestas vayan al admin)
            $mail->addReplyTo(SMTP_USER, CORREO_FROM_NAME);
            
            // AGREGAR ADJUNTOS
            if (!empty($adjuntos) && is_array($adjuntos)) {
                foreach ($adjuntos as $adjunto) {
                    if (isset($adjunto['ruta']) && isset($adjunto['nombre']) && file_exists($adjunto['ruta'])) {
                        $mail->addAttachment($adjunto['ruta'], $adjunto['nombre']);
                    }
                }
            }
            
            // CONTENIDO
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->Body    = self::plantillaCorreo($contenidoHTML);
            $mail->AltBody = strip_tags($contenidoHTML);
            
            // ENVIAR
            $mail->send();
            
            error_log("Correo enviado a cliente: $destinatario");
            return ['success' => true, 'message' => 'Correo enviado'];
            
        } catch (Exception $e) {
            $error = "Error PHPMailer: " . $mail->ErrorInfo;
            error_log($error);
            return ['success' => false, 'message' => $error];
        }
    }
    
    /**
     * Plantilla HTML para correos - CON COLORES VERDES
     */
    private static function plantillaCorreo($contenido) {
        $verdePrimario   = "#7fad39";
        $verdeOscuro     = "#5a8a20";
        
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <link href='https://fonts.googleapis.com/css2?family=Raleway:wght@800&family=Lato:wght@400;700&display=swap' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
            <style>
                @media only screen and (max-width: 600px) {
                    .container { width: 100% !important; border-radius: 0 !important; }
                    .header { padding: 45px 20px !important; }
                    .content { padding: 30px 20px !important; }
                    .responsive-table { width: 100% !important; }
                }
            </style>
        </head>
        <body style='margin: 0; padding: 0; background-color: #f0f4f8; font-family: \"Lato\", Arial, sans-serif;'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                    <td align='center' style='padding: 20px 0;'>
                        
                        <table class='container' width='600' border='0' cellspacing='0' cellpadding='0' style='background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.12); border: 1px solid #e2e8f0;'>
                            
                            <tr>
                                <td class='header' style='background: linear-gradient(135deg, $verdePrimario 0%, $verdeOscuro 100%); padding: 60px 40px; text-align: center;'>
                                    <div style='margin-bottom: 12px;'>
                                        <span style='background: rgba(255,255,255,0.15); color: #ffffff; padding: 6px 16px; border-radius: 50px; font-size: 10px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.3);'>Portal de Contacto</span>
                                    </div>
                                    <h1 style='color: #ffffff; margin: 0; font-family: \"Raleway\", sans-serif; font-size: 42px; font-weight: 800; letter-spacing: -1px; line-height: 1;'>
                                        TANQUES TLALOC
                                    </h1>
                                    <p style='color: #ffffff; margin: 12px 0 0; font-size: 14px; letter-spacing: 6px; opacity: 0.9; text-transform: uppercase; font-weight: 300;'>
                                        Creadores del Tinaco Bala
                                    </p>
                                    <div style='height: 3px; width: 40px; background-color: #ffffff; margin: 25px auto 0; border-radius: 10px; opacity: 0.5;'></div>
                                </td>
                            </tr>
    
                            <tr>
                                <td class='content' style='padding: 50px 45px; background-color: #ffffff;'>
                                    <div style='color: #2D3748; line-height: 1.8; font-size: 16px;'>
                                        $contenido
                                    </div>
                                </td>
                            </tr>
    
                            <tr>
                                <td style='background-color: #0f172a; padding: 45px; border-top: 6px solid $verdePrimario;'>
                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                        <tr>
                                            <td style='text-align: center; padding-bottom: 30px;'>
                                                <h4 style='color: #ffffff; margin: 0; font-family: \"Raleway\", sans-serif; font-size: 18px; text-transform: uppercase; letter-spacing: 2px;'>
                                                    Tanques Tlaloc
                                                </h4>
                                                <p style='color: #94a3b8; font-size: 13px; margin: 10px 0;'>Especialistas en Rotomoldeo con más de 20 años de experiencia.</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table width='100%' border='0' cellspacing='0' cellpadding='0' style='border-top: 1px solid #334155; padding-top: 30px;'>
                                                    <tr>
                                                        <td width='50%' valign='top'>
                                                            <p style='color: $verdePrimario; font-size: 11px; font-weight: bold; margin: 0 0 8px; text-transform: uppercase;'>📍 Ubicación</p>
                                                            <p style='color: #e2e8f0; font-size: 13px; margin: 0; line-height: 1.5;'>" . EMPRESA_DIRECCION . "</p>
                                                        </td>
                                                        <td width='50%' valign='top'>
                                                            <p style='color: $verdePrimario; font-size: 11px; font-weight: bold; margin: 0 0 8px; text-transform: uppercase;'>📞 Contacto</p>
                                                            <p style='color: #e2e8f0; font-size: 13px; margin: 0; line-height: 1.5;'>
                                                                Tel: " . EMPRESA_TELEFONO . "<br>
                                                                Email: " . CORREO_FROM . "
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: center; padding-top: 40px;'>
                                                <p style='color: #475569; font-size: 10px; margin: 0; letter-spacing: 2px; text-transform: uppercase;'>
                                                    © " . date('Y') . " TANQUES TLALOC | ESPECIALISTAS EN ROTOMOLDEO.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
    
                        <p style='text-align: center; color: #94a3b8; font-size: 11px; margin-top: 25px;'>
                            Este es un mensaje automático del sistema de contacto de Tanques Tlaloc.
                        </p>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
    }
}
?>