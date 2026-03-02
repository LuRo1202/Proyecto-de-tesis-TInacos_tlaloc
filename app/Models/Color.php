<?php
// app/Models/Color.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    protected $table = 'colores';
    
    protected $fillable = [
        'nombre',
        'codigo_hex'
    ];

    // Relación: Un color tiene muchos productos
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'color_id');
    }

    // Accesor para mostrar el color con su código
    public function getDisplayAttribute(): string
    {
        return "<span style='color:{$this->codigo_hex}'>{$this->nombre}</span>";
    }
}