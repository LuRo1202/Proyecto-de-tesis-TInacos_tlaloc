<?php
// app/Http/Controllers/ClienteAuthController.php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClienteAuthController extends Controller
{
    /**
     * Procesar registro de cliente
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:clientes,email',
            'password' => 'required|min:6|confirmed',
            'telefono' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Crear cliente
        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'activo' => true
        ]);

        // Iniciar sesión automáticamente
        Auth::guard('cliente')->login($cliente);

        return redirect()->route('cliente.dashboard')->with('success', '¡Registro exitoso!');
    }
}