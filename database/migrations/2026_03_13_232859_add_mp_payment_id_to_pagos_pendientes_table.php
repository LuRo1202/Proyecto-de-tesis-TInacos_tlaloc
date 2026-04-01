<?php
// database/migrations/xxxx_add_mp_payment_id_to_pagos_pendientes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pagos_pendientes', function (Blueprint $table) {
            $table->string('mp_payment_id')->nullable()->after('mp_preference_id');
        });
    }

    public function down()
    {
        Schema::table('pagos_pendientes', function (Blueprint $table) {
            $table->dropColumn('mp_payment_id');
        });
    }
};