<?php
// app/Models/OfertaProducto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfertaProducto extends Model
{
    protected $table = 'oferta_productos';
    
    protected $fillable = [
        'oferta_id',
        'producto_id',
        'precio_oferta'
    ];

    protected $casts = [
        'precio_oferta' => 'decimal:2'
    ];

    // Relación con oferta
    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class, 'oferta_id');
    }

    // Relación con producto
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}