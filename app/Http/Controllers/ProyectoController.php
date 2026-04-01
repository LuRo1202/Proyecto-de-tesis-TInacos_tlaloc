<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ProyectoContactoMail;

class ProyectoController extends Controller
{
    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefono' => 'required|string|max:20',
            'comentarios' => 'required|string|max:2000',
            'privacidad' => 'required|accepted',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $archivoPath = null;
        $archivoInfo = null;

        try {
            // Guardar archivo si existe
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $nombre_original = $archivo->getClientOriginalName();
                $archivoPath = storage_path('app/temp/' . time() . '_' . $nombre_original);
                
                if (!is_dir(storage_path('app/temp'))) {
                    mkdir(storage_path('app/temp'), 0755, true);
                }
                
                $archivo->move(storage_path('app/temp'), basename($archivoPath));
                
                $archivoInfo = [
                    'ruta' => $archivoPath,
                    'nombre' => $nombre_original
                ];
            }

            // Datos para el correo
            $datos = [
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'],
                'comentarios' => $validated['comentarios'],
            ];

            // 1. CORREO PARA ADMIN (con archivo adjunto)
            Mail::to(env('MAIL_NOTIFICACIONES'))
                ->send(new ProyectoContactoMail($datos, $archivoPath, $archivoInfo, 'admin'));
            
            // 2. CORREO PARA CLIENTE (confirmación, sin archivo)
            Mail::to($validated['email'])
                ->send(new ProyectoContactoMail($datos, null, null, 'cliente'));

            // Limpiar archivo temporal
            if ($archivoPath && file_exists($archivoPath)) {
                unlink($archivoPath);
            }

            // ✅ REDIRECCIÓN ORIGINAL A GRACIAS
            return redirect()->route('gracias', [
                'nombre' => $validated['nombre'],
                'tipo' => 'proyecto'
            ]);

        } catch (\Exception $e) {
            if ($archivoPath && file_exists($archivoPath)) {
                unlink($archivoPath);
            }
            
            Log::error('Error en proyecto: ' . $e->getMessage());
            return redirect()->route('contacto')
                ->with('error', 'Error del sistema. Por favor intenta más tarde.')
                ->withInput();
        }
    }
}