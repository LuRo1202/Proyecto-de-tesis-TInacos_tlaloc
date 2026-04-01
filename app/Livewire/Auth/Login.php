<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Usuario;
use App\Models\Cliente;
use App\Helpers\CarritoHelper;

class Login extends Component
{
    public $usuario = '';
    public $password = '';
    public $remember = false;
    public $redirect_to = null; // 👈 AGREGADO

    protected $rules = [
        'usuario' => 'required|string',
        'password' => 'required|string',
    ];

    protected $messages = [
        'usuario.required' => 'El usuario o email es obligatorio.',
        'password.required' => 'La contraseña es obligatoria.',
    ];

    public function mount($redirect_to = null) // 👈 AGREGADO
    {
        $this->redirect_to = $redirect_to;
    }

    public function login()
    {
        $this->validate();

        $usuario = Usuario::where('usuario', $this->usuario)
                    ->orWhere('email', $this->usuario)
                    ->first();
        
        if ($usuario) {
            return $this->autenticarUsuario($usuario);
        }

        $cliente = Cliente::where('email', $this->usuario)->first();
        
        if ($cliente) {
            return $this->autenticarCliente($cliente);
        }

        $this->addError('usuario', 'Las credenciales no coinciden con nuestros registros.');
        $this->dispatch('loginError', 'Usuario no encontrado');
    }

    protected function autenticarUsuario($usuario)
    {
        if (!$usuario->activo) {
            $this->addError('usuario', 'Esta cuenta está desactivada. Contacta al administrador.');
            $this->dispatch('loginError', 'Cuenta desactivada');
            return;
        }

        if (Auth::guard('web')->attempt(
            ['usuario' => $usuario->usuario, 'password' => $this->password], 
            $this->remember
        )) {
            session()->regenerate();
            
            $this->migrarCarrito();
            
            session()->flash('login_success', true);
            session()->flash('login_message', '¡Bienvenido ' . $usuario->nombre . '!');
            
            // 👈 VERIFICAR REDIRECCIÓN AL CARRITO
            if ($this->redirect_to === 'carrito') {
                return redirect()->route('carrito');
            }
            
            $user = auth()->user();
            
            if ($user->rol === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->rol === 'gerente') {
                return redirect()->route('gerente.dashboard');
            } elseif ($user->rol === 'vendedor') {
                return redirect()->route('vendedor.dashboard');
            }
            
            return redirect()->intended(route('home'));
        }

        $this->addError('usuario', 'Las credenciales no coinciden con nuestros registros.');
        $this->dispatch('loginError', 'Credenciales incorrectas');
    }

    protected function autenticarCliente($cliente)
    {
        if (!$cliente->activo) {
            $this->addError('usuario', 'Esta cuenta está desactivada. Contacta al administrador.');
            $this->dispatch('loginError', 'Cuenta desactivada');
            return;
        }

        if (Auth::guard('cliente')->attempt(
            ['email' => $cliente->email, 'password' => $this->password], 
            $this->remember
        )) {
            session()->regenerate();
            
            $this->migrarCarrito();
            
            session()->flash('login_success', true);
            session()->flash('login_message', '¡Bienvenido ' . $cliente->nombre . '!');
            
            // 👈 VERIFICAR REDIRECCIÓN AL CARRITO
            if ($this->redirect_to === 'carrito') {
                return redirect()->route('carrito');
            }
            
            return redirect()->route('cliente.dashboard');
        }

        $this->addError('usuario', 'Las credenciales no coinciden con nuestros registros.');
        $this->dispatch('loginError', 'Credenciales incorrectas');
    }

    /**
     * 🚚 MIGRAR CARRITO DE SESIÓN A BASE DE DATOS
     */
    protected function migrarCarrito()
    {
        $carritoSesion = session()->get('carrito', []);
        
        if (empty($carritoSesion)) {
            Log::info('No hay carrito en sesión para migrar');
            return;
        }
        
        Log::info('Migrando carrito de sesión a BD', [
            'items_count' => count($carritoSesion),
            'user_id' => auth()->id(),
            'guard' => auth()->guard('cliente')->check() ? 'cliente' : 'web'
        ]);
        
        if (Auth::guard('cliente')->check()) {
            $cliente = Auth::guard('cliente')->user();
            
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
            Log::info('Carrito migrado a BD para cliente', [
                'cliente_id' => $cliente->id,
                'items_count' => count($cliente->carrito)
            ]);
        } 
        else if (Auth::guard('web')->check()) {
            Log::info('Usuario admin/gerente/vendedor logueado, no se guarda carrito en BD');
        }
        
        session()->forget('carrito');
        Log::info('Carrito de sesión limpiado después de migración');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}