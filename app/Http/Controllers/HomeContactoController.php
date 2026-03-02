<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mailer\Mailer;

class HomeContactoController extends Controller
{
    /**
     * Procesar el formulario de contacto que está en home.blade.php
     */
    public function enviar(Request $request)
    {
        // Validar los campos
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefono' => 'required|string|max:20',
            'mensaje' => 'required|string|max:1000',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'Correo inválido',
            'telefono.required' => 'El teléfono es obligatorio',
            'mensaje.required' => 'El mensaje es obligatorio',
        ]);

        try {
            // 1. CORREO PARA TI (ADMIN)
            $contenidoAdmin = $this->generarHTMLCorreoAdmin($validated);
            $asuntoAdmin = "Nueva Solicitud de Asesoría - " . $validated['nombre'];
            
            $resultadoAdmin = Mailer::enviarCorreo($asuntoAdmin, $contenidoAdmin);
            
            if (!$resultadoAdmin['success']) {
                Log::error('Error en mailer admin: ' . ($resultadoAdmin['message'] ?? 'Desconocido'));
            }
            
            // 2. CORREO PARA EL CLIENTE (CONFIRMACIÓN)
            $contenidoCliente = $this->generarHTMLCorreoCliente($validated);
            $asuntoCliente = "Hemos recibido tu mensaje - Tanques Tlaloc";
            
            // Enviar al correo del cliente
            $resultadoCliente = Mailer::enviarCorreoCliente(
                $validated['email'], 
                $asuntoCliente, 
                $contenidoCliente
            );
            
            if (!$resultadoCliente['success']) {
                Log::error('Error en mailer cliente: ' . ($resultadoCliente['message'] ?? 'Desconocido'));
            }
            
            // Redirigir a página de gracias
            return redirect()->route('gracias', [
                'nombre' => $validated['nombre'],
                'tipo' => 'contacto'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en contacto home: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'Error del sistema. Por favor intenta más tarde.')
                ->withInput();
        }
    }

    /**
     * Generar HTML para correo del ADMIN
     */
    private function generarHTMLCorreoAdmin($datos)
    {
        $nombre = htmlspecialchars($datos['nombre']);
        $telefono = htmlspecialchars($datos['telefono']);
        $email = htmlspecialchars($datos['email']);
        $mensaje = htmlspecialchars($datos['mensaje']);
        $verde = '#7fad39';
        
        return "
        <h2 style='color: $verde; margin-top: 0;'>NUEVA SOLICITUD DE ASESORÍA</h2>
        <p style='color: #64748b; margin-bottom: 25px; font-size: 14px;'>
            Un cliente ha solicitado información a través del formulario web.
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
                    <td style='padding: 12px 0;'>
                        <strong style='color: #475569;'>Mensaje:</strong>
                    </td>
                    <td style='padding: 12px 0;'>
                        <div style='background-color: #ffffff; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 8px;'>
                            <p style='margin: 0; color: #334155; line-height: 1.6;'>
                                " . nl2br($mensaje) . "
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
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
                        <strong style='color: #475569;'>Origen:</strong>
                        Formulario de Contacto Web - Tanques Tlaloc
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
        ";
    }

    /**
     * Generar HTML para correo del CLIENTE (confirmación)
     */
    private function generarHTMLCorreoCliente($datos)
    {
        $nombre = htmlspecialchars($datos['nombre']);
        $email = htmlspecialchars($datos['email']);
        $mensaje = htmlspecialchars($datos['mensaje']);
        $verde = '#7fad39';
        
        return "
        <div style='text-align: center; margin-bottom: 30px;'>
            <div style='background-color: #f0f9f0; border-radius: 50%; width: 80px; height: 80px; margin: 0 auto 20px auto; display: flex; align-items: center; justify-content: center; text-align: center;'>
                <span style='font-size: 48px; line-height: 80px; color: #7fad39; display: inline-block; width: 100%; height: 100%; vertical-align: middle;'>✓</span>
            </div>
            <h2 style='color: #7fad39; margin: 0;'>¡Gracias por contactarnos!</h2>
        </div>

        <p style='color: #64748b; font-size: 16px; margin-bottom: 25px;'>
            Hola <strong style='color: #1e293b;'>$nombre</strong>,
        </p>

        <p style='color: #334155; line-height: 1.6; margin-bottom: 20px;'>
            Hemos recibido tu mensaje y te responderemos a la brevedad posible.
        </p>

        <div style='background-color: #f8fafc; padding: 20px; border-radius: 12px; margin: 25px 0; border-left: 4px solid $verde;'>
            <h4 style='margin: 0 0 15px 0; color: #1e293b; font-size: 16px;'>
                <i class='fas fa-envelope' style='margin-right: 8px;'></i>Tu mensaje:
            </h4>
            <div style='background-color: #ffffff; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;'>
                <p style='margin: 0; color: #334155; font-style: italic;'>
                    \"$mensaje\"
                </p>
            </div>
        </div>

        <div style='background-color: #f8fafc; padding: 20px; border-radius: 12px; margin: 25px 0;'>
            <h4 style='margin: 0 0 15px 0; color: #1e293b; font-size: 16px;'>
                <i class='fas fa-clock' style='margin-right: 8px;'></i>¿Qué sigue?
            </h4>
            <ul style='color: #334155; line-height: 1.8; padding-left: 20px;'>
                <li>Uno de nuestros asesores revisará tu mensaje</li>
                <li>Te contactaremos en menos de 24 horas hábiles</li>
                <li>Responderemos a este correo: <strong>$email</strong></li>
            </ul>
        </div>

        <p style='color: #64748b; font-size: 14px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;'>
            Si no fuiste tú quien solicitó esta información, por favor ignora este mensaje.
        </p>
        ";
    }
}