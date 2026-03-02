<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Cliente;

class Login extends Component
{
    public $usuario = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'usuario' => 'required|string',
        'password' => 'required|string',
    ];

    protected $messages = [
        'usuario.required' => 'El usuario o email es obligatorio.',
        'password.required' => 'La contraseña es obligatoria.',
    ];

    public function login()
    {
        $this->validate();

        // PASO 1: Buscar en tabla USUARIOS (por usuario o email)
        $usuario = Usuario::where('usuario', $this->usuario)
                    ->orWhere('email', $this->usuario)
                    ->first();
        
        if ($usuario) {
            return $this->autenticarUsuario($usuario);
        }

        // PASO 2: Buscar en tabla CLIENTES (solo por email)
        $cliente = Cliente::where('email', $this->usuario)->first();
        
        if ($cliente) {
            return $this->autenticarCliente($cliente);
        }

        // PASO 3: No existe en ninguna tabla
        $this->addError('usuario', 'Las credenciales no coinciden con nuestros registros.');
        $this->dispatch('loginError', 'Usuario no encontrado');
    }

    protected function autenticarUsuario($usuario)
    {
        // Verificar si está activo
        if (!$usuario->activo) {
            $this->addError('usuario', 'Esta cuenta está desactivada. Contacta al administrador.');
            $this->dispatch('loginError', 'Cuenta desactivada');
            return;
        }

        // Intentar autenticación con guard de usuarios
        if (Auth::guard('web')->attempt(
            ['usuario' => $usuario->usuario, 'password' => $this->password], 
            $this->remember
        )) {
            session()->regenerate();
            
            // Mensaje de éxito para SweetAlert
            session()->flash('login_success', true);
            session()->flash('login_message', '¡Bienvenido ' . $usuario->nombre . '!');
            
            $user = auth()->user();
            
            // Redirigir según rol
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
        // Verificar si está activo
        if (!$cliente->activo) {
            $this->addError('usuario', 'Esta cuenta está desactivada. Contacta al administrador.');
            $this->dispatch('loginError', 'Cuenta desactivada');
            return;
        }

        // Intentar autenticación con guard de clientes
        if (Auth::guard('cliente')->attempt(
            ['email' => $cliente->email, 'password' => $this->password], 
            $this->remember
        )) {
            session()->regenerate();
            
            // Mensaje de éxito para SweetAlert
            session()->flash('login_success', true);
            session()->flash('login_message', '¡Bienvenido ' . $cliente->nombre . '!');
            
            return redirect()->route('cliente.dashboard');
        }

        $this->addError('usuario', 'Las credenciales no coinciden con nuestros registros.');
        $this->dispatch('loginError', 'Credenciales incorrectas');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}