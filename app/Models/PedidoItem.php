<?php
// app/Models/PedidoItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItem extends Model
{
    protected $table = 'pedido_items';
    
    protected $fillable = [
        'pedido_id',
        'producto_id',
        'producto_nombre',
        'cantidad',
        'precio'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2'
    ];

    // Relación con pedido
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Relación con producto
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Calcular subtotal
    public function getSubtotalAttribute(): float
    {
        return $this->cantidad * $this->precio;
    }
}