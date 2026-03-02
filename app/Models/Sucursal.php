<?php
// app/Models/Sucursal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'latitud',
        'longitud',
        'radio_cobertura_km',
        'activa',
        'horario'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'radio_cobertura_km' => 'integer'
    ];

    // Relación: Sucursal tiene muchos usuarios (vendedores)
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuario_sucursal')
                    ->withPivot('fecha_asignacion')
                    ->withTimestamps();
    }

    // Relación: Sucursal tiene muchos productos (inventario)
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_sucursal')
                    ->withPivot('existencias', 'stock_minimo', 'stock_maximo', 'fecha_actualizacion')
                    ->withTimestamps();
    }

    // Relación: Sucursal tiene muchos pedidos
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'sucursal_id');
    }

    // Verificar si una dirección está dentro de la cobertura (con latitud/longitud)
    public function estaEnCobertura($latitud, $longitud): bool
    {
        // Fórmula de Haversine para calcular distancia
        $earthRadius = 6371; // Radio de la Tierra en km
        
        // Convertir a float para evitar errores
        $latFrom = deg2rad((float)$this->latitud);
        $lonFrom = deg2rad((float)$this->longitud);
        $latTo = deg2rad((float)$latitud);
        $lonTo = deg2rad((float)$longitud);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        $distancia = $angle * $earthRadius;
        
        return $distancia <= $this->radio_cobertura_km;
    }
    
    /**
     * Verificar cobertura para una dirección usando Google Maps
     */
    public function verificarCobertura($direccion, $ciudad, $estado)
    {
        try {
            $apiKey = config('services.google.maps_api_key');
            
            if (!$apiKey) {
                // Si no hay API key, usar simulación
                return $this->verificarCoberturaSimulada($direccion, $ciudad, $estado);
            }
            
            // Construir dirección completa
            $direccionCompleta = $direccion . ', ' . $ciudad . ', ' . $estado . ', México';
            
            // Obtener coordenadas de la dirección del cliente
            $clienteCoords = $this->obtenerCoordenadas($direccionCompleta, $apiKey);
            
            if (!$clienteCoords) {
                // Si no se pueden obtener coordenadas, usar simulación
                return $this->verificarCoberturaSimulada($direccion, $ciudad, $estado);
            }
            
            // Calcular distancia usando Haversine
            $distancia = $this->calcularDistanciaHaversine(
                $clienteCoords['lat'],
                $clienteCoords['lng']
            );
            
            $distanciaRedondeada = round($distancia, 2);
            $valido = $distancia <= $this->radio_cobertura_km;
            
            return [
                'valido' => $valido,
                'distancia' => $distanciaRedondeada,
                'mensaje' => $valido 
                    ? "✅ ¡Sí llegamos! Estás a {$distanciaRedondeada} km de nuestra sucursal en {$this->nombre}"
                    : "❌ Lo sentimos, estás a {$distanciaRedondeada} km y nuestro radio es {$this->radio_cobertura_km} km",
                'coords' => $clienteCoords
            ];
            
        } catch (\Exception $e) {
            Log::error('Error en verificarCobertura: ' . $e->getMessage());
            return $this->verificarCoberturaSimulada($direccion, $ciudad, $estado);
        }
    }

    /**
     * Obtener coordenadas de una dirección usando Google Maps Geocoding API
     */
    private function obtenerCoordenadas($direccion, $apiKey)
    {
        try {
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" 
                   . urlencode($direccion) . "&key=" . $apiKey;
            
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
            
            if ($data['status'] == 'OK' && isset($data['results'][0]['geometry']['location'])) {
                return [
                    'lat' => $data['results'][0]['geometry']['location']['lat'],
                    'lng' => $data['results'][0]['geometry']['location']['lng']
                ];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo coordenadas: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calcular distancia usando fórmula de Haversine
     */
    private function calcularDistanciaHaversine($latCliente, $lngCliente)
    {
        $earthRadius = 6371; // Radio de la Tierra en km
        
        $latFrom = deg2rad((float)$this->latitud);
        $lonFrom = deg2rad((float)$this->longitud);
        $latTo = deg2rad((float)$latCliente);
        $lonTo = deg2rad((float)$lngCliente);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    /**
     * Versión simulada para fallback (cuando no hay API key)
     */
    private function verificarCoberturaSimulada($direccion, $ciudad, $estado)
    {
        $distancia = rand(2, 7);
        $valido = $distancia <= $this->radio_cobertura_km;
        
        return [
            'valido' => $valido,
            'distancia' => $distancia,
            'mensaje' => $valido 
                ? "✅ ¡Sí llegamos! Estás a {$distancia} km de nuestra sucursal en {$this->nombre}"
                : "❌ Lo sentimos, estás a {$distancia} km y nuestro radio es {$this->radio_cobertura_km} km",
            'coords' => null
        ];
    }

    // Scope para sucursales activas
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}