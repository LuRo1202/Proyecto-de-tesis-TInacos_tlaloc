<?php
// app/Helpers/CoberturaHelper.php

namespace App\Helpers;

use App\Models\Sucursal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CoberturaHelper
{
    /**
     * Calcular distancia usando Google Maps API con caché
     */
    public static function calcularDistancia($origen, $destino)
    {
        $cacheKey = 'distancia_' . md5($origen . $destino);
        
        return Cache::remember($cacheKey, 3600, function() use ($origen, $destino) {
            try {
                $apiKey = config('services.google.maps_api_key');
                
                if (!$apiKey) {
                    \Log::warning('Google Maps API key no configurada');
                    return null;
                }

                $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                    'origins' => $origen,
                    'destinations' => $destino,
                    'key' => $apiKey,
                    'units' => 'metric',
                    'language' => 'es'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['rows'][0]['elements'][0]['distance']['value'])) {
                        // Distancia en metros, convertir a km
                        return round($data['rows'][0]['elements'][0]['distance']['value'] / 1000, 2);
                    }
                }
                
                return null;
            } catch (\Exception $e) {
                \Log::error('Error calculando distancia: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Verificar cobertura de todas las sucursales para una dirección
     */
    public static function verificarCoberturaMultiple($direccion, $ciudad, $estado)
    {
        $sucursales = Sucursal::where('activa', true)->get();
        $resultados = [];
        
        $destino = $direccion . ', ' . $ciudad . ', ' . $estado . ', México';
        
        foreach ($sucursales as $sucursal) {
            $origen = $sucursal->direccion;
            $distancia = self::calcularDistancia($origen, $destino);
            
            if ($distancia !== null) {
                $resultados[] = [
                    'sucursal' => $sucursal,
                    'distancia' => $distancia,
                    'dentro_cobertura' => $distancia <= $sucursal->radio_cobertura_km,
                    'radio' => $sucursal->radio_cobertura_km
                ];
            }
        }
        
        // Ordenar por distancia
        usort($resultados, function($a, $b) {
            return $a['distancia'] <=> $b['distancia'];
        });
        
        return $resultados;
    }

    /**
     * Encuentra la mejor sucursal para una dirección
     */
    public static function encontrarMejorSucursal($direccion, $ciudad, $estado)
    {
        $resultados = self::verificarCoberturaMultiple($direccion, $ciudad, $estado);
        
        foreach ($resultados as $resultado) {
            if ($resultado['dentro_cobertura']) {
                return $resultado;
            }
        }
        
        return null;
    }

    /**
     * Verifica si una dirección está dentro de cobertura de alguna sucursal
     */
    public static function verificarCobertura($sucursal, $direccion, $ciudad, $estado)
    {
        $origen = $sucursal->direccion;
        $destino = $direccion . ', ' . $ciudad . ', ' . $estado . ', México';
        
        $distancia = self::calcularDistancia($origen, $destino);
        
        if ($distancia === null) {
            return [
                'valido' => false,
                'mensaje' => 'No se pudo calcular la distancia',
                'distancia' => null
            ];
        }

        if ($distancia <= $sucursal->radio_cobertura_km) {
            return [
                'valido' => true,
                'mensaje' => "✅ ¡Sí llegamos! Estás a {$distancia} km de nuestra sucursal en {$sucursal->nombre}",
                'distancia' => $distancia,
                'sucursal' => $sucursal
            ];
        } else {
            return [
                'valido' => false,
                'mensaje' => "❌ Lo sentimos, no llegamos a tu zona. Estás a {$distancia} km y nuestro radio máximo es {$sucursal->radio_cobertura_km} km.",
                'distancia' => $distancia,
                'sucursal' => $sucursal
            ];
        }
    }
}