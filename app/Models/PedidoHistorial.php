<?php
// app/Models/PedidoHistorial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoHistorial extends Model
{
    protected $table = 'pedido_historial';
    
    protected $fillable = [
        'pedido_id',
        'usuario_id',
        'accion',
        'detalles',
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    // Relación con pedido
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Relación con usuario
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Registrar acción en historial
    public static function registrar($pedidoId, $usuarioId, $accion, $detalles = null)
    {
        return self::create([
            'pedido_id' => $pedidoId,
            'usuario_id' => $usuarioId,
            'accion' => $accion,
            'detalles' => $detalles,
            'fecha' => now()
        ]);
    }
}