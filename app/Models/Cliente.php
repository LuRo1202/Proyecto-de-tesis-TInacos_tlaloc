<?php
// app/Models/Cliente.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Authenticatable
{
    use Notifiable;

    protected $table = 'clientes';
    
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'direccion',
        'ciudad',
        'estado',
        'codigo_postal',
        'activo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
    ];

    // Relación: Cliente tiene muchos pedidos
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    // Obtener pedidos recientes
    public function pedidosRecientes($limite = 5)
    {
        return $this->pedidos()
                    ->orderBy('created_at', 'desc')
                    ->limit($limite)
                    ->get();
    }

    // Calcular total gastado
    public function getTotalGastadoAttribute()
    {
        return $this->pedidos()
                    ->where('estado', '!=', 'cancelado')
                    ->sum('total');
    }

    // Scope para clientes activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope para búsqueda
    public function scopeBuscar($query, $termino)
    {
        if ($termino) {
            return $query->where('nombre', 'LIKE', "%{$termino}%")
                         ->orWhere('email', 'LIKE', "%{$termino}%")
                         ->orWhere('telefono', 'LIKE', "%{$termino}%");
        }
        return $query;
    }
}