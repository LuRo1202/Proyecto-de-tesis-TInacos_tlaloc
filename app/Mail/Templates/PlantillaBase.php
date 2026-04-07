<?php

namespace App\Mail\Templates;

class PlantillaBase
{
    /**
     * Plantilla principal de correos - COMPATIBLE CON OUTLOOK
     * Usa solo tablas y estilos inline que Outlook soporta
     */
    public static function render($contenido)
    {
        $verdePrimario = "#7fad39";
        $verdeOscuro = "#5a8a20";
        
        $empresaDireccion = "Av Morelos Oriente 186 a, Colonia San Cristobal Centro, Ecatepec de Morelos, Estado de México";
        $empresaTelefono = "+52 55 4017 5803";
        $correoFrom = config('mail.from.address');
        $anio = date('Y');
        
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanques Tláloc</title>
    <style>
        /* Estilos solo para clientes que los soportan */
        @media only screen and (max-width: 600px) {
            .responsive-table { width: 100% !important; }
            .responsive-padding { padding: 20px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f0f4f8; font-family: Arial, Helvetica, sans-serif;">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f0f4f8">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                
                <!-- Contenedor principal -->
                <table class="responsive-table" width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="border-collapse: collapse; border: 1px solid #e2e8f0;">
                    
                    <!-- HEADER - Versión simplificada para Outlook -->
                    <tr>
                        <td bgcolor="{$verdePrimario}" style="background-color: {$verdePrimario}; padding: 40px 30px; text-align: center;">
                            <!-- Tabla interna para mantener el color -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <div style="background-color: rgba(255,255,255,0.15); display: inline-block; padding: 5px 15px; border-radius: 50px; margin-bottom: 15px;">
                                            <span style="color: #ffffff; font-size: 10px; font-weight: bold; letter-spacing: 2px;">PORTAL DE CONTACTO</span>
                                        </div>
                                        <h1 style="color: #ffffff; margin: 0; font-size: 36px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">
                                            TANQUES TLÁLOC
                                        </h1>
                                        <p style="color: #ffffff; margin: 10px 0 0; font-size: 12px; letter-spacing: 4px;">
                                            Creadores del Tinaco Bala
                                        </p>
                                        <div style="height: 3px; width: 40px; background-color: #ffffff; margin: 20px auto 0;"></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- CONTENIDO -->
                    <tr>
                        <td style="padding: 40px 35px; background-color: #ffffff;">
                            <div style="color: #2D3748; line-height: 1.6; font-size: 15px; font-family: Arial, Helvetica, sans-serif;">
                                {$contenido}
                            </div>
                        </td>
                    </tr>
                    
                    <!-- FOOTER -->
                    <tr>
                        <td bgcolor="#0f172a" style="background-color: #0f172a; padding: 35px; border-top: 5px solid {$verdePrimario};">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 25px;">
                                        <h4 style="color: #ffffff; margin: 0; font-size: 16px; font-family: Arial, Helvetica, sans-serif;">
                                            Tanques Tláloc
                                        </h4>
                                        <p style="color: #94a3b8; font-size: 12px; margin: 10px 0 0;">
                                            Especialistas en Rotomoldeo con más de 20 años de experiencia.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 1px solid #334155; padding-top: 25px;">
                                            <tr>
                                                <td width="50%" valign="top" style="text-align: left;">
                                                    <p style="color: {$verdePrimario}; font-size: 11px; font-weight: bold; margin: 0 0 8px;">📍 UBICACIÓN</p>
                                                    <p style="color: #e2e8f0; font-size: 12px; margin: 0; line-height: 1.5;">{$empresaDireccion}</p>
                                                </td>
                                                <td width="50%" valign="top" style="text-align: left;">
                                                    <p style="color: {$verdePrimario}; font-size: 11px; font-weight: bold; margin: 0 0 8px;">📞 CONTACTO</p>
                                                    <p style="color: #e2e8f0; font-size: 12px; margin: 0; line-height: 1.5;">
                                                        Tel: {$empresaTelefono}<br>
                                                        Email: {$correoFrom}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 30px;">
                                        <p style="color: #475569; font-size: 10px; margin: 0;">
                                            © {$anio} TANQUES TLÁLOC | ESPECIALISTAS EN ROTOMOLDEO
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <!-- Mensaje automático -->
                <p style="text-align: center; color: #94a3b8; font-size: 11px; margin-top: 20px;">
                    Este es un mensaje automático del sistema de Tanques Tláloc.
                </p>
                
            </td>
        </tr>
    </table>
    
</body>
</html>
HTML;
    }
}