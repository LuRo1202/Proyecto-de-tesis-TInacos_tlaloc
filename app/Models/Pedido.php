<?php
// app/Models/Pedido.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pedido extends Model
{
    protected $table = 'pedidos';
    
    protected $fillable = [
        'cliente_id',
        'folio',
        'cliente_nombre',
        'cliente_telefono',
        'cliente_direccion',
        'cliente_ciudad',
        'cliente_estado',
        'codigo_postal',
        'total',
        'metodo_pago',
        'pago_confirmado',
        'estado',
        'fecha_confirmacion',
        'fecha_entrega',
        'notas',
        'sucursal_id',
        'distancia_km',
        'cobertura_verificada'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'fecha_entrega' => 'date',
        'pago_confirmado' => 'boolean',
        'cobertura_verificada' => 'boolean',
        'total' => 'decimal:2',
        'distancia_km' => 'decimal:2'
    ];

    // Relación con cliente
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // Relación con sucursal
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    // Relación con items
    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }

    // Relación con historial
    public function historial(): HasMany
    {
        return $this->hasMany(PedidoHistorial::class, 'pedido_id');
    }

    // Relación con responsables (vendedores)
    public function responsables(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'pedido_responsables', 'pedido_id', 'usuario_id')
                    ->withPivot('fecha_asignacion')
                    ->withTimestamps();
    }

    // Verificar si tiene cliente registrado
    public function getTieneClienteRegistradoAttribute(): bool
    {
        return !is_null($this->cliente_id);
    }

    // Generar folio automático
    public static function generarFolio(): string
    {
        $fecha = now()->format('ymd');
        $ultimo = self::whereDate('created_at', today())->count();
        $numero = str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
        
        return "PED-{$fecha}-{$numero}";
    }

    // Actualizar total del pedido
    public function actualizarTotal()
    {
        $this->total = $this->items()->sum(\DB::raw('cantidad * precio'));
        $this->saveQuietly();
    }

    // Scope por estado
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    // Scope para pedidos pendientes
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['pendiente', 'confirmado']);
    }

    // Scope por cliente
    public function scopeDeCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    // Scope por rango de fechas
    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('created_at', [$inicio, $fin]);
    }
}