<?php
// database/migrations/2026_02_28_000003_create_sucursales_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('direccion');
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->integer('radio_cobertura_km')->default(8);
            $table->boolean('activa')->default(true);
            $table->text('horario')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sucursales');
    }
};