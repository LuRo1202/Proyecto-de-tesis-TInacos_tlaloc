<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Notifications\ResetPasswordNotification;

class Cliente extends Authenticatable
{
    use Notifiable, CanResetPassword;

    protected $table = 'clientes';
    
    // 👇 SOLO AGREGAR 'carrito' aquí (no borrar nada)
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'direccion',
        'ciudad',
        'estado',
        'codigo_postal',
        'activo',
        'carrito'  // ← SOLO AGREGAR ESTA LÍNEA
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 👇 SOLO AGREGAR 'carrito' => 'array' aquí
    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
        'carrito' => 'array',  // ← SOLO AGREGAR ESTA LÍNEA
    ];

    // El resto del código SIGUE IGUAL, no toques nada más
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, 'cliente'));
    }

    public function pedidosRecientes($limite = 5)
    {
        return $this->pedidos()
                    ->orderBy('created_at', 'desc')
                    ->limit($limite)
                    ->get();
    }

    public function getTotalGastadoAttribute()
    {
        return $this->pedidos()
                    ->where('estado', '!=', 'cancelado')
                    ->sum('total');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

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