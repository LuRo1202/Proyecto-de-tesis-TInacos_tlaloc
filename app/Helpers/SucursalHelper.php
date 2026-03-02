<?php
// app/Helpers/SucursalHelper.php

namespace App\Helpers;

use App\Models\Sucursal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class SucursalHelper
{
    /**
     * Obtiene la sucursal actual según el dominio o sesión
     */
    public static function getSucursalActual()
    {
        // 1. Intentar obtener de la sesión
        if (Session::has('sucursal_actual')) {
            return Session::get('sucursal_actual');
        }

        // 2. Determinar por dominio o usar por defecto
        $sucursalId = self::determinarSucursalPorDominio();
        
        // 3. Buscar en caché o BD
        $sucursal = Cache::remember("sucursal_{$sucursalId}", 3600, function() use ($sucursalId) {
            return Sucursal::with('productos')->find($sucursalId);
        });

        // 4. Guardar en sesión
        if ($sucursal) {
            Session::put('sucursal_actual', $sucursal);
        }

        return $sucursal;
    }

    /**
     * Determina qué sucursal usar según el dominio
     */
    protected static function determinarSucursalPorDominio()
    {
        $dominio = request()->getHost();
        $host = request()->getHost();
        
        // Mapeo de dominios a IDs de sucursales
        $sucursalesPorDominio = [
            'ecatepec.tinacos.test' => 1,
            'sanluis.tinacos.test' => 2,
            'monterrey.tinacos.test' => 3,
            'localhost' => env('SUCURSAL_DEFAULT_ID', 1),
            '127.0.0.1' => env('SUCURSAL_DEFAULT_ID', 1)
        ];

        foreach ($sucursalesPorDominio as $dominioMap => $id) {
            if (str_contains($host, $dominioMap)) {
                return $id;
            }
        }

        // Por parámetro en URL (para pruebas)
        if (request()->has('sucursal')) {
            return request()->get('sucursal');
        }

        // Default
        return env('SUCURSAL_DEFAULT_ID', 1);
    }

    /**
     * Cambia la sucursal actual (útil para pruebas)
     */
    public static function cambiarSucursal($sucursalId)
    {
        Session::forget('sucursal_actual');
        Cache::forget("sucursal_{$sucursalId}");
        
        return self::getSucursalActual();
    }

    /**
     * Obtiene los productos con stock de la sucursal actual
     */
    public static function getProductosConStock($categoriaId = null)
    {
        $sucursal = self::getSucursalActual();
        
        if (!$sucursal) {
            return collect();
        }

        $query = $sucursal->productos()
            ->wherePivot('existencias', '>', 0)
            ->where('activo', true)
            ->withPivot('existencias', 'stock_minimo', 'stock_maximo', 'fecha_actualizacion')
            ->with(['categoria', 'color', 'ofertas']);

        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }

        return $query->get();
    }

    /**
     * Verifica si un producto tiene stock en la sucursal actual
     */
    public static function tieneStock($productoId, $cantidad = 1)
    {
        $sucursal = self::getSucursalActual();
        
        if (!$sucursal) {
            return false;
        }

        $producto = $sucursal->productos()
            ->where('producto_id', $productoId)
            ->first();

        return $producto && $producto->pivot->existencias >= $cantidad;
    }

    /**
     * Obtiene productos con stock bajo
     */
    public static function getProductosStockBajo($limite = 10)
    {
        $sucursal = self::getSucursalActual();
        
        if (!$sucursal) {
            return collect();
        }

        return $sucursal->productos()
            ->wherePivot('existencias', '>', 0)
            ->wherePivot('existencias', '<=', \DB::raw('producto_sucursal.stock_minimo'))
            ->withPivot('existencias', 'stock_minimo')
            ->with(['categoria', 'color'])
            ->limit($limite)
            ->get();
    }

    /**
     * Limpia la caché de la sucursal
     */
    public static function limpiarCache()
    {
        $sucursal = self::getSucursalActual();
        if ($sucursal) {
            Cache::forget("sucursal_{$sucursal->id}");
        }
        Session::forget('sucursal_actual');
    }
}