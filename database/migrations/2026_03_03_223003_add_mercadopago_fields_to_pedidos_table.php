<?php
// database/migrations/2024_03_04_000001_add_mercadopago_fields_to_pedidos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMercadopagoFieldsToPedidosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // ID de la preferencia (cuando creamos el pago)
            $table->string('mp_preference_id')
                  ->nullable()
                  ->after('total')
                  ->comment('ID de la preferencia en Mercado Pago');

            // ID del pago (cuando se realiza)
            $table->string('mp_payment_id')
                  ->nullable()
                  ->after('mp_preference_id')
                  ->comment('ID del pago en Mercado Pago');

            // Estado del pago
            $table->string('mp_status')
                  ->nullable()
                  ->after('mp_payment_id')
                  ->comment('Estado del pago: approved, pending, rejected');

            // Detalle del estado
            $table->string('mp_status_detail')
                  ->nullable()
                  ->after('mp_status')
                  ->comment('Detalle del estado en MP');

            // Respuesta completa de MP
            $table->json('mp_response')
                  ->nullable()
                  ->after('mp_status_detail')
                  ->comment('Respuesta completa de Mercado Pago');

            // Índices para búsquedas rápidas
            $table->index('mp_preference_id');
            $table->index('mp_payment_id');
            $table->index('mp_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'mp_preference_id',
                'mp_payment_id',
                'mp_status',
                'mp_status_detail',
                'mp_response'
            ]);
        });
    }
}