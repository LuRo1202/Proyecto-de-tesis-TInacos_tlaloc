<?php
// database/migrations/2026_02_28_000012_create_pedido_responsables_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pedido_responsables', function (Blueprint $table) {
            $table->id('id_pedido_responsable');
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->timestamps();
            
            $table->unique(['pedido_id', 'usuario_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedido_responsables');
    }
};