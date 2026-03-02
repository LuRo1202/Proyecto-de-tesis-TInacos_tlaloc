<?php
// database/migrations/2026_02_28_000002_create_colores_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('colores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('codigo_hex', 7)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('colores');
    }
};