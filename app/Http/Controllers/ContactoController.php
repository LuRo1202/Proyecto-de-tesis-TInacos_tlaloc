<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\SucursalHelper;

class ContactoController extends Controller
{
    public function index()
    {
        $sucursal = SucursalHelper::getSucursalActual();
        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_values($cart));
        
        return view('contacto', compact('sucursal', 'cartCount'));
    }

    
}