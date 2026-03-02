<?php
// app/Helpers/GerenteHelper.php

namespace App\Helpers;

use App\Models\Usuario;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;

class GerenteHelper
{
    /**
     * Obtener el gerente autenticado
     */
    public static function getGerente()
    {
        $user = Auth::user();
        
        if (!$user || $user->rol !== 'gerente') {
            return null;
        }
        
        return $user;
    }

    /**
     * Obtener la sucursal del gerente autenticado
     */
    public static function getSucursalGerente()
    {
        $user = self::getGerente();
        
        if (!$user) {
            return null;
        }
        
        // Obtener la primera sucursal del gerente
        $sucursal = $user->sucursales()->first();
        
        return $sucursal;
    }

    /**
     * Verificar que el usuario es gerente y tiene sucursal
     */
    public static function checkGerente()
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Debes iniciar sesión');
        }
        
        if ($user->rol !== 'gerente') {
            abort(403, 'Acceso no autorizado. Se requieren permisos de gerente.');
        }
        
        $sucursal = self::getSucursalGerente();
        
        if (!$sucursal) {
            abort(403, 'No tienes una sucursal asignada.');
        }
        
        return [
            'gerente' => $user,
            'sucursal' => $sucursal
        ];
    }

    /**
     * Obtener estadísticas de la sucursal del gerente
     */
    public static function getEstadisticas()
    {
        $data = self::checkGerente();
        $sucursal = $data['sucursal'];
        
        $hoy = now()->startOfDay();
        $semana = now()->startOfWeek();
        $mes = now()->startOfMonth();
        
        return [
            'pedidos_hoy' => $sucursal->pedidos()->whereDate('created_at', $hoy)->count(),
            'pedidos_semana' => $sucursal->pedidos()->where('created_at', '>=', $semana)->count(),
            'pedidos_mes' => $sucursal->pedidos()->where('created_at', '>=', $mes)->count(),
            'productos_stock_bajo' => SucursalHelper::getProductosStockBajo()->count(),
            'total_ingresos_hoy' => $sucursal->pedidos()->whereDate('created_at', $hoy)->sum('total'),
            'total_ingresos_mes' => $sucursal->pedidos()->where('created_at', '>=', $mes)->sum('total'),
        ];
    }

    /**
     * Obtener los vendedores de la sucursal
     */
    public static function getVendedores()
    {
        $data = self::checkGerente();
        $sucursal = $data['sucursal'];
        
        return $sucursal->usuarios()
            ->where('rol', 'vendedor')
            ->where('activo', true)
            ->get();
    }
}