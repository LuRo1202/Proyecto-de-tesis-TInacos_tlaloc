<?php
// app/View/Composers/VendedorSidebarComposer.php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Pedido;
use Carbon\Carbon;

class VendedorSidebarComposer
{
    public function compose(View $view)
    {
        $usuario = Auth::user();
        
        if (!$usuario) {
            return;
        }
        
        $usuario_id = $usuario->id;
        
        // Obtener sucursal del vendedor
        $sucursal = $usuario->sucursales()->first();
        
        if (!$sucursal) {
            $view->with([
                'sidebar_pedidos_asignados' => 0,
                'sidebar_pedidos_hoy' => 0,  // CAMBIADO: de urgentes a hoy
                'sidebar_ventas_mes' => 0
            ]);
            return;
        }
        
        $sucursal_id = $sucursal->id;
        
        // ===== CONTADOR 1: MIS PEDIDOS (asignados a mí, pendientes o confirmados) =====
        $pedidosAsignadosCount = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->count();
        
        // ===== CONTADOR 2: PEDIDOS DE HOY (disponibles + míos) - ¡CORREGIDO! =====
        $pedidosHoyCount = Pedido::where('sucursal_id', $sucursal_id)
            ->whereDate('fecha', Carbon::today())
            ->where(function($query) use ($usuario_id) {
                $query->whereDoesntHave('responsables') // Sin asignar (disponibles)
                      ->orWhereHas('responsables', function($q) use ($usuario_id) {
                          $q->where('usuario_id', $usuario_id); // Mis pedidos
                      });
            })
            ->count();
        
        // ===== CONTADOR 3: VENTAS DEL MES (mis entregados) =====
        $ventasMesCount = Pedido::whereHas('responsables', function($q) use ($usuario_id) {
                $q->where('usuario_id', $usuario_id);
            })
            ->where('sucursal_id', $sucursal_id)
            ->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->where('estado', 'entregado')
            ->count();

        // Pasar los contadores a la vista
        $view->with([
            'sidebar_pedidos_asignados' => $pedidosAsignadosCount,
            'sidebar_pedidos_hoy' => $pedidosHoyCount,  // CAMBIADO: ahora se llama 'sidebar_pedidos_hoy'
            'sidebar_ventas_mes' => $ventasMesCount
        ]);
    }
}