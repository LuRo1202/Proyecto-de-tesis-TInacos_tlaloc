<?php
// app/Models/Categoria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';
    
    protected $fillable = [
        'nombre'
    ];

    // Relación: Una categoría tiene muchos productos
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    // Scope para búsqueda
    public function scopeBuscar($query, $termino)
    {
        if ($termino) {
            return $query->where('nombre', 'LIKE', "%{$termino}%");
        }
        return $query;
    }
}