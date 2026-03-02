<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mailer\Mailer;

class ProyectoController extends Controller
{
    /**
     * Procesar el formulario de proyecto (con archivos adjuntos)
     */
    public function enviar(Request $request)
    {
        // Validar los campos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefono' => 'required|string|max:20',
            'comentarios' => 'required|string|max:2000',
            'privacidad' => 'required|accepted',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB = 5120 KB
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'Correo inválido',
            'telefono.required' => 'El teléfono es obligatorio',
            'comentarios.required' => 'La descripción del proyecto es obligatoria',
            'privacidad.required' => 'Debes aceptar la política de privacidad',
            'privacidad.accepted' => 'Debes aceptar la política de privacidad',
            'archivo.max' => 'El archivo no debe pesar más de 5MB',
            'archivo.mimes' => 'El archivo debe ser PDF, JPG, JPEG o PNG',
        ]);

        try {
            // Procesar archivo adjunto
            $adjuntos = [];
            $ruta_temporal = null;
            
            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $archivo = $request->file('archivo');
                
                // Guardar archivo temporalmente en storage
                $nombre_original = $archivo->getClientOriginalName();
                $ruta_temporal = storage_path('app/temp/' . time() . '_' . $nombre_original);
                
                // Crear directorio temp si no existe
                if (!is_dir(storage_path('app/temp'))) {
                    mkdir(storage_path('app/temp'), 0755, true);
                }
                
                // Mover archivo temporal
                $archivo->move(storage_path('app/temp'), basename($ruta_temporal));
                
                // Agregar a adjuntos
                $adjuntos[] = [
                    'ruta' => $ruta_temporal,
                    'nombre' => $nombre_original
                ];
            }
            
            // 1. CORREO PARA TI (ADMIN) - con adjuntos
            $contenidoAdmin = $this->generarHTMLProyectoAdmin($validated, $adjuntos ? $adjuntos[0] : null);
            $asuntoAdmin = "Nueva Solicitud de Proyecto - " . $validated['nombre'];
            
            $resultadoAdmin = Mailer::enviarCorreo($asuntoAdmin, $contenidoAdmin, $adjuntos);
            
            if (!$resultadoAdmin['success']) {
                Log::error('Error en mailer admin (proyecto): ' . ($resultadoAdmin['message'] ?? 'Desconocido'));
            }
            
            // 2. CORREO PARA EL CLIENTE (CONFIRMACIÓN) - sin adjuntos
            $contenidoCliente = $this->generarHTMLProyectoCliente($validated);
            $asuntoCliente = "Hemos recibido tu solicitud de proyecto - Tanques Tlaloc";
            
            $resultadoCliente = Mailer::enviarCorreoCliente(
                $validated['email'],
                $asuntoCliente,
                $contenidoCliente
            );
            
            if (!$resultadoCliente['success']) {
                Log::error('Error en mailer cliente (proyecto): ' . ($resultadoCliente['message'] ?? 'Desconocido'));
            }
            
            // Eliminar archivo temporal después de enviar
            if ($ruta_temporal && file_exists($ruta_temporal)) {
                unlink($ruta_temporal);
            }
            
            return redirect()->route('gracias', [
                'nombre' => $validated['nombre'],
                'tipo' => 'proyecto'
            ]);
            
        } catch (\Exception $e) {
            // Eliminar archivo temporal si existe
            if (isset($ruta_temporal) && file_exists($ruta_temporal)) {
                unlink($ruta_temporal);
            }
            
            Log::error('Error en proyecto: ' . $e->getMessage());
            return redirect()->route('contacto')
                ->with('error', 'Error del sistema. Por favor intenta más tarde.')
                ->withInput();
        }
    }
    
    /**
     * Generar HTML para correo del ADMIN (con detalles del proyecto)
     */
    private function generarHTMLProyectoAdmin($datos, $archivo_info = null)
    {
        $nombre = htmlspecialchars($datos['nombre']);
        $email = htmlspecialchars($datos['email']);
        $telefono = htmlspecialchars($datos['telefono']);
        $comentarios = htmlspecialchars($datos['comentarios']);
        $verde = '#7fad39';
        
        // Función para formatear bytes
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
     * Generar HTML para correo del CLIENTE (confirmación)
     */
    private function generarHTMLProyectoCliente($datos)
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