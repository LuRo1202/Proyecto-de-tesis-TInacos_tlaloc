<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
<<<<<<< HEAD
use Illuminate\Support\Facades\Log;
=======
use Illuminate\Support\Facades\Log; // 👈 AGREGAR para logs
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90

class ClienteAuthController extends Controller
{
    public function register(Request $request)
    {
        // Validación manual con mensajes personalizados en español
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:clientes,email',
            'password' => 'required|min:6|confirmed',
            'telefono' => 'required|string|max:20|unique:clientes,telefono',
        ], [
            // Mensajes personalizados en español
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico no es válido',
            'email.unique' => 'El correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.unique' => 'El número de teléfono ya está registrado',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $cliente = Cliente::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'activo' => true
            ]);

            Auth::guard('cliente')->login($cliente);

<<<<<<< HEAD
            if ($request->redirect_to === 'carrito') {
                $this->migrarCarrito($cliente);
                return redirect()->route('carrito')->with('success', '¡Registro exitoso! Ahora puedes comprar.');
            }

            return redirect()->route('cliente.dashboard')->with('success', '¡Registro exitoso!');

        } catch (\Exception $e) {
            Log::error('Error al registrar: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }

    protected function migrarCarrito($cliente)
    {
        $carritoSesion = session()->get('carrito', []);
        if (empty($carritoSesion)) return;
        
        if ($cliente->carrito && !empty($cliente->carrito)) {
            $carritoBD = $cliente->carrito;
            foreach ($carritoSesion as $id => $item) {
                if (isset($carritoBD[$id])) {
                    $carritoBD[$id]['cantidad'] += $item['cantidad'];
                } else {
                    $carritoBD[$id] = $item;
                }
            }
            $cliente->carrito = $carritoBD;
        } else {
            $cliente->carrito = $carritoSesion;
        }
        
        $cliente->save();
        session()->forget('carrito');
=======
        // 👈 VERIFICAR SI VIENE DEL CARRITO
        if ($request->redirect_to === 'carrito') {
            // Migrar carrito de sesión a BD
            $this->migrarCarrito($cliente);
            
            return redirect()->route('carrito')->with('success', '¡Registro exitoso! Ahora puedes comprar.');
        }

        // Redirección normal
        return redirect()->route('cliente.dashboard')->with('success', '¡Registro exitoso!');
>>>>>>> 85af045c5f0b497a0abb1ea6f580b495fe7bbb90
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