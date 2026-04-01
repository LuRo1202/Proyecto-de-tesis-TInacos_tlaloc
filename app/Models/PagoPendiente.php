<?php
// app/Models/PagoPendiente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoPendiente extends Model
{
    protected $table = 'pagos_pendientes';
    
// app/Models/PagoPendiente.php
    protected $fillable = [
        'folio',
        'cliente_id',
        'checkout_data',
        'mp_preference_id',
        'mp_payment_id',  // 👈 AGREGAR ESTO
        'status'
    ];
    
    protected $casts = [
        'checkout_data' => 'array'
    ];
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}