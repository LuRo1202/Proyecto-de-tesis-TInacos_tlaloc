<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
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

    // 👇 MÉTODO OBLIGATORIO
    public function getAuthPassword()
    {
        return $this->contrasena_hash;
    }

    // 👇 MODIFICAR ESTE MÉTODO - Verificar si ya está hasheado
    public function setContrasenaHashAttribute($value)
    {
        // Si el valor no parece un hash Bcrypt (no empieza con $2y$), lo hasheamos
        if (!str_starts_with($value, '$2y$') && !str_starts_with($value, '$2a$')) {
            $this->attributes['contrasena_hash'] = Hash::make($value);
        } else {
            $this->attributes['contrasena_hash'] = $value;
        }
    }

    // Verificar contraseña
    public function verificarPassword($password): bool
    {
        return Hash::check($password, $this->contrasena_hash);
    }

    // El resto de tus métodos igual...
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

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeRol($query, $rol)
    {
        return $query->where('rol', $rol);
    }
}