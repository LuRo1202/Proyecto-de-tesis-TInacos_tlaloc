<?php
// app/Models/Oferta.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Oferta extends Model
{
    protected $table = 'ofertas';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'valor',
        'fecha_inicio',
        'fecha_fin',
        'activa'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'activa' => 'boolean',
        'valor' => 'decimal:2'
    ];

    // Relación con productos
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'oferta_productos')
                    ->withPivot('precio_oferta')
                    ->withTimestamps();
    }

    // Verificar si la oferta está vigente
    public function getEstaVigenteAttribute(): bool
    {
        $ahora = Carbon::now();
        return $this->activa && 
               $ahora >= $this->fecha_inicio && 
               $ahora <= $this->fecha_fin;
    }

    // Calcular precio con descuento
    public function calcularPrecioConDescuento(float $precioOriginal): float
    {
        if ($this->tipo === 'porcentaje') {
            return round($precioOriginal * (1 - $this->valor / 100), 2);
        } else {
            return round(max(0, $precioOriginal - $this->valor), 2);
        }
    }

    // Obtener productos con precio ya calculado
    public function productosConPrecio()
    {
        return $this->productos()->get()->map(function($producto) {
            $producto->precio_con_descuento = $this->calcularPrecioConDescuento($producto->precio);
            return $producto;
        });
    }

    // Scope para ofertas vigentes
    public function scopeVigente($query)
    {
        $ahora = Carbon::now();
        return $query->where('activa', true)
                     ->where('fecha_inicio', '<=', $ahora)
                     ->where('fecha_fin', '>=', $ahora);
    }

    // Scope para ofertas activas (sin importar fechas)
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    // Scope para ofertas que inician pronto
    public function scopeProximas($query)
    {
        $ahora = Carbon::now();
        return $query->where('activa', true)
                     ->where('fecha_inicio', '>', $ahora)
                     ->orderBy('fecha_inicio', 'asc');
    }
}