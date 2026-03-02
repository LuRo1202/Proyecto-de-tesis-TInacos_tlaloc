<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

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
     * Enviar enlace de reset al email
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

        // Intentar enviar el enlace usando el broker de clientes
        $status = Password::broker('clientes')->sendResetLink(
            $request->only('email')
        );

        // Verificar el resultado
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', '¡Hemos enviado el enlace de recuperación a tu correo!');
        }

        // Si hay error (email no existe)
        return back()->withErrors(['email' => 'No encontramos un usuario con ese correo electrónico.']);
    }
}