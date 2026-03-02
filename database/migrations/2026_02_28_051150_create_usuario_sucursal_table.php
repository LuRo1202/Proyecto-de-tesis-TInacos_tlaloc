<?php
// database/migrations/2026_02_28_000005_create_usuario_sucursal_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario_sucursal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->timestamps();
            
            $table->unique(['usuario_id', 'sucursal_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_sucursal');
    }
};