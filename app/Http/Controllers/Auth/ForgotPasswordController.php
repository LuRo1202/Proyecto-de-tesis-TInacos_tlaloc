<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\Cliente;
use App\Models\Usuario;

class ForgotPasswordController extends Controller
{
    /**
     * Mostrar formulario para solicitar reset de password
     */
    public function showLinkRequestForm()
    {
        return view('pages.auth.reset-password');
    }

    /**
     * Enviar enlace de reset al email (UNIVERSAL - busca en clientes y usuarios)
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validar el email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Buscar en ambas tablas
        $cliente = Cliente::where('email', $request->email)->first();
        $usuario = Usuario::where('email', $request->email)->first();

        // Si no existe en ninguna tabla
        if (!$cliente && !$usuario) {
            return back()->withErrors(['email' => 'No encontramos un usuario con ese correo electrónico.']);
        }

        // Determinar qué broker usar y enviar el correo
        if ($cliente) {
            // Es un cliente
            $status = Password::broker('clientes')->sendResetLink($request->only('email'));
        } else {
            // Es un usuario (admin/gerente/vendedor)
            $status = Password::broker('users')->sendResetLink($request->only('email'));
        }

        // Verificar el resultado
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', '¡Hemos enviado el enlace de recuperación a tu correo!');
        }

        return back()->withErrors(['email' => 'Error al enviar el correo.']);
    }
}