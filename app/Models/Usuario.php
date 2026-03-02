<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPasswordNotification; // 👈 IMPORTAR LA NOTIFICACIÓN UNIFICADA

class Usuario extends Authenticatable
{
    use Notifiable, CanResetPassword;

    protected $table = 'usuarios';
    
    protected $fillable = [
        'usuario',
        'contrasena_hash',
        'nombre',
        'email',
        'rol',
        'activo'
    ];

    protected $hidden = [
        'contrasena_hash',
        'remember_token'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime'
    ];

    // 👇 Laravel espera un campo 'password' por defecto
    public function getAuthPassword()
    {
        return $this->contrasena_hash;
    }

    // 👇 Para que Laravel encuentre el campo password
    public function getPasswordAttribute()
    {
        return $this->contrasena_hash;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['contrasena_hash'] = $value;
    }

    public function setContrasenaHashAttribute($value)
    {
        if (!str_starts_with($value, '$2y$') && !str_starts_with($value, '$2a$')) {
            $this->attributes['contrasena_hash'] = Hash::make($value);
        } else {
            $this->attributes['contrasena_hash'] = $value;
        }
    }

    // 👇 Método requerido para reset password (CORREGIDO)
    public function sendPasswordResetNotification($token)
    {
        // 👈 AHORA USA LA NOTIFICACIÓN UNIFICADA CON TIPO 'usuario'
        $this->notify(new ResetPasswordNotification($token, 'usuario'));
    }

    public function verificarPassword($password): bool
    {
        return Hash::check($password, $this->contrasena_hash);
    }

    // Relaciones
    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(Sucursal::class, 'usuario_sucursal')
                    ->withPivot('fecha_asignacion')
                    ->withTimestamps();
    }

    public function pedidosResponsable()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_responsables')
                    ->withPivot('fecha_asignacion')
                    ->withTimestamps();
    }

    public function historiales(): HasMany
    {
        return $this->hasMany(PedidoHistorial::class, 'usuario_id');
    }

    // Roles
    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function isGerente(): bool
    {
        return $this->rol === 'gerente';
    }

    public function isVendedor(): bool
    {
        return $this->rol === 'vendedor';
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeRol($query, $rol)
    {
        return $query->where('rol', $rol);
    }
}