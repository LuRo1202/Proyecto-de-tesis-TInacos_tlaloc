<?php
// app/Models/ProductoSucursal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoSucursal extends Model
{
    protected $table = 'producto_sucursal';
    
    protected $fillable = [
        'producto_id',
        'sucursal_id',
        'existencias',
        'stock_minimo',
        'stock_maximo',
        'fecha_actualizacion'
    ];

    protected $casts = [
        'existencias' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'fecha_actualizacion' => 'datetime'
    ];

    // Relación con producto
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Relación con sucursal
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    // Verificar si está bajo stock mínimo
    public function getBajoStockAttribute(): bool
    {
        return $this->existencias <= $this->stock_minimo;
    }

    // Verificar si está sobre stock máximo
    public function getSobreStockAttribute(): bool
    {
        return $this->stock_maximo > 0 && $this->existencias >= $this->stock_maximo;
    }
}