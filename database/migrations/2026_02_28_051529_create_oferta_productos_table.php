<?php
// database/migrations/2026_02_28_000014_create_oferta_productos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oferta_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oferta_id')->constrained('ofertas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->decimal('precio_oferta', 10, 2)->nullable()->comment('Precio ya calculado');
            $table->timestamps();
            
            $table->unique(['oferta_id', 'producto_id']);
            $table->index('producto_id');
            $table->index('oferta_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('oferta_productos');
    }
};