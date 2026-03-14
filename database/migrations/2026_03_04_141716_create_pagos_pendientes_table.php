<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pagos_pendientes', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->unsignedBigInteger('cliente_id');
            $table->json('checkout_data'); // Guarda TODO en formato JSON
            $table->string('mp_preference_id')->nullable();
            $table->string('status')->default('pendiente');
            $table->timestamps();
            
            // Llave foránea
            $table->foreign('cliente_id')
                  ->references('id')
                  ->on('clientes')
                  ->onDelete('cascade');
                  
            // Índices para búsquedas rápidas
            $table->index('folio');
            $table->index('mp_preference_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_pendientes');
    }
};