<?php
// app/Models/Producto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'litros',
        'categoria_id',
        'color_id',
        'precio',
        'activo',
        'destacado'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'destacado' => 'boolean',
        'precio' => 'decimal:2',
        'litros' => 'integer'
    ];

    // Relación con categoría
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    // Relación con color
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    // Relación con sucursales (inventario)
    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(Sucursal::class, 'producto_sucursal')
                    ->withPivot('existencias', 'stock_minimo', 'stock_maximo', 'fecha_actualizacion')
                    ->withTimestamps();
    }

    // Relación con ofertas
    public function ofertas(): BelongsToMany
    {
        return $this->belongsToMany(Oferta::class, 'oferta_productos')
                    ->withPivot('precio_oferta')
                    ->withTimestamps();
    }

    // Relación con items de pedido
    public function itemsPedido(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'producto_id');
    }

    // Obtener la oferta vigente actual
    public function getOfertaVigenteAttribute()
    {
        return $this->ofertas()
                    ->vigente()
                    ->first();
    }

    // Obtener precio final (con descuento si aplica)
    public function getPrecioFinalAttribute()
    {
        $oferta = $this->oferta_vigente;
        
        if ($oferta) {
            return $oferta->calcularPrecioConDescuento($this->precio);
        }
        
        return $this->precio;
    }

    // Saber si está en oferta
    public function getEnOfertaAttribute()
    {
        return $this->ofertas()->vigente()->exists();
    }

    // Obtener porcentaje de descuento
    public function getPorcentajeDescuentoAttribute()
    {
        $oferta = $this->oferta_vigente;
        
        if ($oferta && $oferta->tipo === 'porcentaje') {
            return $oferta->valor;
        }
        
        return 0;
    }

    // Scope para productos activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope para productos destacados
    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }

    // Scope para productos en oferta
    public function scopeEnOferta($query)
    {
        return $query->whereHas('ofertas', function($q) {
            $q->vigente();
        });
    }

    // Scope por categoría
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    // Scope para búsqueda
    public function scopeBuscar($query, $termino)
    {
        if ($termino) {
            return $query->where('nombre', 'LIKE', "%{$termino}%")
                         ->orWhere('codigo', 'LIKE', "%{$termino}%");
        }
        return $query;
    }
}