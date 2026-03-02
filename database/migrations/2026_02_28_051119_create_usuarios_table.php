<?php
// database/migrations/2026_02_28_000004_create_usuarios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('usuario', 50)->unique();
            $table->string('contrasena_hash', 255);
            $table->string('nombre', 100);
            $table->string('email', 100)->nullable()->unique();
            $table->enum('rol', ['admin', 'gerente', 'vendedor'])->default('vendedor');
            $table->boolean('activo')->default(true);
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};