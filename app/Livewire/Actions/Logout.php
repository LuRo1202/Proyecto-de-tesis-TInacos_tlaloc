<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{

    public function __invoke()
    {
        $userName = Auth::check() ? Auth::user()->nombre : '';
        
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('login')
            ->with('logout_success', true)
            ->with('logout_message', "Sesión cerrada exitosamente" . ($userName ? " - $userName" : ''));
    }
}