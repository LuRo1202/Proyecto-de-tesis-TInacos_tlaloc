<?php
// app/Http/Controllers/ClienteAuthController.php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // 👈 AGREGAR para logs

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

        // 👈 VERIFICAR SI VIENE DEL CARRITO
        if ($request->redirect_to === 'carrito') {
            // Migrar carrito de sesión a BD
            $this->migrarCarrito($cliente);
            
            return redirect()->route('carrito')->with('success', '¡Registro exitoso! Ahora puedes comprar.');
        }

        // Redirección normal
        return redirect()->route('cliente.dashboard')->with('success', '¡Registro exitoso!');
    }

    /**
     * 🚚 MIGRAR CARRITO DE SESIÓN A BASE DE DATOS
     */
    protected function migrarCarrito($cliente)
    {
        // Obtener carrito de sesión
        $carritoSesion = session()->get('carrito', []);
        
        if (empty($carritoSesion)) {
            Log::info('No hay carrito en sesión para migrar en registro');
            return;
        }
        
        Log::info('Migrando carrito de sesión a BD desde registro', [
            'items_count' => count($carritoSesion),
            'cliente_id' => $cliente->id
        ]);
        
        // Si ya tiene carrito en BD, combinarlos
        if ($cliente->carrito && !empty($cliente->carrito)) {
            $carritoBD = $cliente->carrito;
            
            // Combinar carritos (sumar cantidades si el producto ya existe)
            foreach ($carritoSesion as $id => $item) {
                if (isset($carritoBD[$id])) {
                    $carritoBD[$id]['cantidad'] += $item['cantidad'];
                } else {
                    $carritoBD[$id] = $item;
                }
            }
            $cliente->carrito = $carritoBD;
        } else {
            // No tiene carrito en BD, asignar el de sesión
            $cliente->carrito = $carritoSesion;
        }
        
        $cliente->save();
        
        Log::info('Carrito migrado a BD para cliente desde registro', [
            'cliente_id' => $cliente->id,
            'items_count' => count($cliente->carrito)
        ]);
        
        // 🧹 Limpiar carrito de sesión después de migrar
        session()->forget('carrito');
        Log::info('Carrito de sesión limpiado después de migración desde registro');
    }
}