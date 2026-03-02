<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Models\Cliente;
use App\Models\Usuario;
use DB; // 👈 IMPORTANTE

class ResetPasswordController extends Controller
{
    /**
     * Mostrar formulario para resetear password (cuando llega con token)
     */
    public function showResetForm(Request $request, $token = null)
    {
        // 👈 VERIFICAR SI EL TOKEN EXISTE ANTES DE MOSTRAR EL FORMULARIO
        $tokenExists = DB::table('password_reset_tokens')
                        ->where('email', $request->email)
                        ->exists();

        if (!$tokenExists) {
            return redirect()->route('login')
                ->with('error', 'El enlace de recuperación ya ha sido utilizado o ha expirado.');
        }

        return view('pages.auth.reset-password-form')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Resetear la contraseña (UNIVERSAL - detecta automáticamente)
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

        // Detectar automáticamente qué broker usar
        $cliente = Cliente::where('email', $request->email)->first();
        $usuario = Usuario::where('email', $request->email)->first();

        if (!$cliente && !$usuario) {
            return back()->withErrors(['email' => 'No encontramos un usuario con ese correo electrónico.']);
        }

        // Usar el broker correspondiente
        if ($cliente) {
            $broker = 'clientes';
        } else {
            $broker = 'users';
        }

        // Intentar resetear la contraseña con el broker detectado
        $status = Password::broker($broker)->reset(
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
            // 👈 ELIMINAR EL TOKEN MANUALMENTE (por si acaso)
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            return redirect()->route('login')->with('status', '¡Tu contraseña ha sido restablecida correctamente!');
        }

        // Si hay error
        return back()->withErrors(['email' => [__($status)]]);
    }
}