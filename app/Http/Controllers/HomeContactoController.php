<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactoHomeMail;

class HomeContactoController extends Controller
{
    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefono' => 'required|string|max:20',
            'mensaje' => 'required|string|max:1000',
        ]);

        try {
            // 1. CORREO PARA ADMIN
            Mail::to(env('MAIL_NOTIFICACIONES'))
                ->send(new ContactoHomeMail($validated, 'admin'));
            
            // 2. CORREO PARA CLIENTE
            Mail::to($validated['email'])
                ->send(new ContactoHomeMail($validated, 'cliente'));
            
            // ✅ REDIRECCIÓN ORIGINAL A GRACIAS
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
}