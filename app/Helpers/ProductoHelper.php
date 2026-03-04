<?php
// app/Helpers/ProductoHelper.php

namespace App\Helpers;

use App\Models\Producto;
use App\Models\Color;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
     * Obtiene la imagen del producto - VERSIÓN CORREGIDA PARA PRODUCCIÓN
     */
    public static function obtenerImagenProducto($codigo)
    {
        // Limpiar el código (NO forzar mayúsculas/minúsculas)
        $codigoOriginal = trim($codigo);
        
        // Intentar con caché para no buscar en disco siempre
        $cacheKey = "producto_img_" . md5($codigoOriginal);
        
        return Cache::remember($cacheKey, 3600, function() use ($codigoOriginal) {
            
            // Ruta principal
            $rutaPrincipal = public_path('assets/img/productos/');
            
            // Extensiones posibles
            $extensiones = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            // ===== 1. BUSCAR CON EL NOMBRE EXACTO (como se guardó) =====
            foreach ($extensiones as $ext) {
                $archivo = $rutaPrincipal . $codigoOriginal . '.' . $ext;
                if (file_exists($archivo)) {
                    return asset('assets/img/productos/' . $codigoOriginal . '.' . $ext);
                }
            }
            
            // ===== 2. BUSCAR EN MAYÚSCULAS (por si acaso) =====
            $codigoUpper = strtoupper($codigoOriginal);
            foreach ($extensiones as $ext) {
                $archivo = $rutaPrincipal . $codigoUpper . '.' . $ext;
                if (file_exists($archivo)) {
                    return asset('assets/img/productos/' . $codigoUpper . '.' . $ext);
                }
            }
            
            // ===== 3. BUSCAR EN MINÚSCULAS (por si acaso) =====
            $codigoLower = strtolower($codigoOriginal);
            foreach ($extensiones as $ext) {
                $archivo = $rutaPrincipal . $codigoLower . '.' . $ext;
                if (file_exists($archivo)) {
                    return asset('assets/img/productos/' . $codigoLower . '.' . $ext);
                }
            }
            
            // ===== 4. BUSCAR POR TIPO (con diferentes variaciones) =====
            $tipos = [
                'TIN-' => ['tinaco', 'TINACO', 'Tinaco'],
                'BALA-' => ['tinaco-bala', 'BALA', 'bala', 'tinacobala'],
                'CIS-' => ['cisterna', 'CISTERNA', 'Cisterna'],
                'ACC-' => ['accesorio', 'ACCESORIO', 'Accesorio'],
                'DISP-' => ['dispensador', 'DISPENSADOR', 'Dispensador'],
                'TOL-' => ['tolva', 'TOLVA', 'Tolva']
            ];
            
            foreach ($tipos as $prefijo => $nombres) {
                if (strpos($codigoOriginal, $prefijo) === 0 || 
                    strpos($codigoUpper, $prefijo) === 0 || 
                    strpos($codigoLower, $prefijo) === 0) {
                    
                    foreach ($nombres as $nombre) {
                        foreach ($extensiones as $ext) {
                            $archivo = $rutaPrincipal . $nombre . '.' . $ext;
                            if (file_exists($archivo)) {
                                return asset('assets/img/productos/' . $nombre . '.' . $ext);
                            }
                        }
                    }
                }
            }
            
            // ===== 5. BUSCAR CUALQUIER ARCHIVO QUE CONTENGA EL CÓDIGO =====
            if (is_dir($rutaPrincipal)) {
                $archivos = scandir($rutaPrincipal);
                foreach ($archivos as $archivo) {
                    if ($archivo != '.' && $archivo != '..') {
                        $info = pathinfo($archivo);
                        $nombreSinExt = $info['filename'];
                        
                        // Si el nombre del archivo contiene parte del código
                        if (stripos($nombreSinExt, substr($codigoOriginal, 0, 6)) !== false ||
                            stripos($codigoOriginal, $nombreSinExt) !== false) {
                            return asset('assets/img/productos/' . $archivo);
                        }
                    }
                }
            }
            
            // ===== 6. PLACEHOLDER POR DEFECTO =====
            $placeholders = ['placeholder.jpg', 'default.jpg', 'no-image.jpg', 'producto-default.jpg'];
            foreach ($placeholders as $placeholder) {
                if (file_exists($rutaPrincipal . $placeholder)) {
                    return asset('assets/img/productos/' . $placeholder);
                }
            }
            
            // Último recurso: logo de la empresa
            return asset('assets/img/logo.jpeg');
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

    /**
     * Formatea porcentaje sin decimales
     */
    public static function formatoPorcentaje($porcentaje)
    {
        return intval($porcentaje);
    }

    /**
     * MÉTODO DE DIAGNÓSTICO - Solo para depuración
     */
    public static function diagnosticarImagen($codigo)
    {
        $resultado = [
            'codigo' => $codigo,
            'pasos' => [],
            'archivos_encontrados' => []
        ];
        
        $rutaPrincipal = public_path('assets/img/productos/');
        $resultado['pasos'][] = "Ruta: " . $rutaPrincipal;
        $resultado['pasos'][] = "Carpeta existe: " . (is_dir($rutaPrincipal) ? 'Sí' : 'No');
        
        if (is_dir($rutaPrincipal)) {
            $archivos = scandir($rutaPrincipal);
            foreach ($archivos as $archivo) {
                if ($archivo != '.' && $archivo != '..') {
                    $resultado['archivos_encontrados'][] = $archivo;
                }
            }
        }
        
        return $resultado;
    }
}