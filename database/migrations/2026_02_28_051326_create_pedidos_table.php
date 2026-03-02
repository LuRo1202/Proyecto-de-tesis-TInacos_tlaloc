<?php
// database/migrations/2026_02_28_000009_create_pedidos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('folio', 20)->unique();
            $table->string('cliente_nombre', 100);
            $table->string('cliente_telefono', 20);
            $table->text('cliente_direccion');
            $table->string('cliente_ciudad', 100);
            $table->string('cliente_estado', 100);
            $table->string('codigo_postal', 10)->nullable();
            $table->decimal('total', 10, 2);
            $table->string('metodo_pago', 50)->default('en_linea');
            $table->boolean('pago_confirmado')->default(false);
            $table->enum('estado', ['pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
            $table->timestamp('fecha')->useCurrent();
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales');
            $table->decimal('distancia_km', 8, 2)->nullable();
            $table->boolean('cobertura_verificada')->default(false);
            $table->timestamps();
            
            $table->index('estado');
            $table->index('fecha');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};