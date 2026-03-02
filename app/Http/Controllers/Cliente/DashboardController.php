<?php
// app/Http/Controllers/Cliente/DashboardController.php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $cliente = Auth::guard('cliente')->user();
        
        // Obtener pedidos del cliente
        $pedidos = $cliente->pedidos()->orderBy('created_at', 'desc')->get();
        
        // Calcular total gastado
        $totalGastado = $pedidos->where('estado', '!=', 'cancelado')->sum('total');
        
        return view('cliente.dashboard', compact('cliente', 'pedidos', 'totalGastado'));
    }
}