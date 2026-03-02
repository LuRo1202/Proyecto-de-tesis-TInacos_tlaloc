<?php
// database/migrations/2026_02_28_000013_create_ofertas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ofertas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['porcentaje', 'fijo'])->default('porcentaje');
            $table->decimal('valor', 10, 2);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->boolean('activa')->default(true);
            $table->timestamps();
            
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index('activa');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ofertas');
    }
};