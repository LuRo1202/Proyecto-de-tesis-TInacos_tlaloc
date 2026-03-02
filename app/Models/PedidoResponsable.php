<?php
// app/Models/PedidoResponsable.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoResponsable extends Model
{
    protected $table = 'pedido_responsables';
    protected $primaryKey = 'id_pedido_responsable';
    
    protected $fillable = [
        'pedido_id',
        'usuario_id',
        'fecha_asignacion'
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime'
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
}