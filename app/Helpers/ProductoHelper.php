<?php
// app/Helpers/ProductoHelper.php

namespace App\Helpers;

use App\Models\Producto;
use App\Models\Color;
use Illuminate\Support\Facades\Cache;

class ProductoHelper
{
    /**
     * Formatea precio a moneda mexicana
     */
    public static function formatoPrecio($precio)
    {
        return '$' . number_format($precio, 2);
    }

    /**
     * Obtiene el precio formateado con descuento si aplica
     */
    public static function formatoPrecioConDescuento($producto)
    {
        if ($producto->en_oferta) {
            $original = self::formatoPrecio($producto->precio);
            $final = self::formatoPrecio($producto->precio_final);
            $descuento = $producto->porcentaje_descuento;
            
            return [
                'html' => "<span class='precio-original tachado'>$original</span> <span class='precio-oferta'>$final</span> <span class='badge badge-success'>-{$descuento}%</span>",
                'original' => $original,
                'final' => $final,
                'descuento' => $descuento
            ];
        }
        
        return [
            'html' => "<span class='precio-normal'>" . self::formatoPrecio($producto->precio) . "</span>",
            'final' => self::formatoPrecio($producto->precio)
        ];
    }

    /**
     * Obtiene la información de la variante (color, diámetro, etc.)
     */
    public static function obtenerInfoVariante($producto)
    {
        // Si el producto ya tiene un color asignado en BD, usar ese
        if ($producto->color) {
            return [
                'tipo' => 'color',
                'nombre' => $producto->color->nombre,
                'icono' => 'fas fa-palette',
                'hex' => $producto->color->codigo_hex ?? '#ccc',
                'color_id' => $producto->color->id
            ];
        }
        
        // Si no tiene color asignado, inferir del código
        return self::obtenerInfoVariantePorCodigo($producto->codigo);
    }

    /**
     * Obtiene información de variante por código (CUANDO NO HAY COLOR EN BD)
     */
    public static function obtenerInfoVariantePorCodigo($codigo)
    {
        // IMPORTANTE: Verificar sufijos más largos PRIMERO
        if (strpos($codigo, '-RM') !== false) {
            return [
                'tipo' => 'color',
                'nombre' => 'Rosa',
                'icono' => 'fas fa-palette',
                'hex' => '#FF69B4',
                'color_id' => 8
            ];
        } elseif (strpos($codigo, '-MD') !== false) {
            return [
                'tipo' => 'mayor-diametro',
                'nombre' => 'Mayor Diámetro',
                'icono' => 'fas fa-arrows-alt-h',
                'hex' => '#ccc',
                'color_id' => null
            ];
        } elseif (strpos($codigo, '-AZ') !== false) {
            return [
                'tipo' => 'color',
                'nombre' => 'Azul Rey',
                'icono' => 'fas fa-palette',
                'hex' => '#4169E1',
                'color_id' => 5
            ];
        } elseif (strpos($codigo, '-N') !== false) {
            return [
                'tipo' => 'color',
                'nombre' => 'Negro',
                'icono' => 'fas fa-palette',
                'hex' => '#000000',
                'color_id' => 1
            ];
        } elseif (strpos($codigo, '-R') !== false) {
            return [
                'tipo' => 'color',
                'nombre' => 'Rojo',
                'icono' => 'fas fa-palette',
                'hex' => '#FF0000',
                'color_id' => 7
            ];
        } elseif (strpos($codigo, '-M') !== false) {
            return [
                'tipo' => 'color',
                'nombre' => 'Morado',
                'icono' => 'fas fa-palette',
                'hex' => '#800080',
                'color_id' => 10
            ];
        } elseif (strpos($codigo, '-B') !== false) {
            return [
                'tipo' => 'color',
                'nombre' => 'Blanco',
                'icono' => 'fas fa-palette',
                'hex' => '#FFFFFF',
                'color_id' => 2
            ];
        } elseif (strpos($codigo, '-C') !== false) {
            return [
                'tipo' => 'color',
                'nombre' => 'Beige',
                'icono' => 'fas fa-palette',
                'hex' => '#F5F5DC',
                'color_id' => 3
            ];
        } elseif (strpos($codigo, 'DISP-20') === 0) {
            return [
                'tipo' => 'color',
                'nombre' => 'Beige',
                'icono' => 'fas fa-palette',
                'hex' => '#F5F5DC',
                'color_id' => 3
            ];
        } else {
            return [
                'tipo' => 'standard',
                'nombre' => 'Estándar',
                'icono' => 'fas fa-cube',
                'hex' => '#ccc',
                'color_id' => null
            ];
        }
    }

    /**
     * Obtiene la imagen del producto
     */
    public static function obtenerImagenProducto($codigo)
    {
        // Intentar con caché para no buscar en disco siempre
        return Cache::remember("producto_img_{$codigo}", 3600, function() use ($codigo) {
            $codigoLimpio = strtoupper(trim($codigo));
            
            // Rutas posibles
            $rutas = [
                public_path('assets/img/productos/'),
                public_path('assets/img/products/'),
                public_path('storage/productos/')
            ];
            
            // Extensiones posibles
            $extensiones = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
            
            // Buscar imagen específica por código
            foreach ($rutas as $ruta) {
                foreach ($extensiones as $ext) {
                    if (file_exists($ruta . $codigoLimpio . $ext)) {
                        return asset('assets/img/productos/' . $codigoLimpio . $ext);
                    }
                }
            }
            
            // Si no encuentra, buscar por tipo
            $tipos = [
                'TIN-' => 'tinaco',
                'BALA-' => 'tinaco-bala',
                'CIS-' => 'cisterna',
                'ACC-' => 'accesorio',
                'DISP-' => 'dispensador',
                'TOL-' => 'tolva'
            ];
            
            foreach ($tipos as $prefijo => $tipo) {
                if (strpos($codigoLimpio, $prefijo) === 0) {
                    foreach ($rutas as $ruta) {
                        foreach ($extensiones as $ext) {
                            if (file_exists($ruta . $tipo . $ext)) {
                                return asset('assets/img/productos/' . $tipo . $ext);
                            }
                        }
                    }
                }
            }
            
            // Placeholder por defecto
            return asset('assets/img/productos/placeholder.jpg');
        });
    }

    /**
     * Obtiene los colores disponibles para un producto base
     */
    public static function obtenerColoresDisponibles($productoBase)
    {
        $nombreBase = preg_replace('/\s+(Negro|Blanco|Beige|Azul|Rojo).*$/i', '', $productoBase->nombre);
        
        return Producto::where('nombre', 'LIKE', $nombreBase . '%')
                      ->where('activo', true)
                      ->with('color')
                      ->get()
                      ->map(function($p) {
                          return [
                              'id' => $p->id,
                              'codigo' => $p->codigo,
                              'color' => $p->color ? $p->color->nombre : 'Estándar',
                              'hex' => $p->color ? $p->color->codigo_hex : '#ccc',
                              'precio' => $p->precio,
                              'precio_final' => $p->precio_final,
                              'en_oferta' => $p->en_oferta
                          ];
                      });
    }

    /**
     * Obtener stock en sucursal específica
     */
    public static function obtenerStock($producto, $sucursalId)
    {
        if ($producto && $sucursalId) {
            $pivot = $producto->sucursales()
                ->where('sucursal_id', $sucursalId)
                ->first();
                
            return $pivot ? $pivot->pivot->existencias : 0;
        }
        return 0;
    }

    /**
     * Verificar disponibilidad en sucursal
     */
    public static function estaDisponible($producto, $sucursalId, $cantidad = 1)
    {
        $stock = self::obtenerStock($producto, $sucursalId);
        return $stock >= $cantidad;
    }

    public static function formatoPorcentaje($porcentaje)
    {
        return intval($porcentaje);
    }
}