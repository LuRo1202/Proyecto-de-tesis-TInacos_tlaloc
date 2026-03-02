<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Mostrar formulario para resetear password (cuando llega con token)
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('pages.auth.reset-password-form')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Resetear la contraseña
     */
    public function reset(Request $request)
    {
        // Validar los datos
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Intentar resetear la contraseña
        $status = Password::broker('clientes')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        // Verificar resultado
        if ($status === Password::PASSWORD_RESET) {
            // 👈 MENSAJE DE ÉXITO CON REDIRECCIÓN A LOGIN
            return redirect()->route('login')->with('status', '¡Tu contraseña ha sido restablecida correctamente!');
        }

        // Si hay error
        return back()->withErrors(['email' => [__($status)]]);
    }
}