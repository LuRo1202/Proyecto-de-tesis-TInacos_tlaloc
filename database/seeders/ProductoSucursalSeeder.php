<?php
// database/seeders/ProductoSucursalSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSucursalSeeder extends Seeder
{
    public function run()
    {
        $stock = [
            ['producto_id' => 1, 'sucursal_id' => 1, 'existencias' => 12, 'stock_minimo' => 5, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-25 05:05:44', 'created_at' => null, 'updated_at' => '2026-02-25 05:05:44'],
            ['producto_id' => 2, 'sucursal_id' => 1, 'existencias' => 9, 'stock_minimo' => 5, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-21 07:24:32', 'created_at' => null, 'updated_at' => '2026-02-21 13:24:32'],
            ['producto_id' => 3, 'sucursal_id' => 1, 'existencias' => 7, 'stock_minimo' => 5, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-25 05:20:17', 'created_at' => null, 'updated_at' => '2026-02-25 05:20:17'],
            ['producto_id' => 4, 'sucursal_id' => 1, 'existencias' => 8, 'stock_minimo' => 5, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 5, 'sucursal_id' => 1, 'existencias' => 9, 'stock_minimo' => 5, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-24 06:12:51', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 6, 'sucursal_id' => 1, 'existencias' => 6, 'stock_minimo' => 5, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 7, 'sucursal_id' => 1, 'existencias' => 39, 'stock_minimo' => 10, 'stock_maximo' => 100, 'fecha_actualizacion' => '2026-02-25 03:24:25', 'created_at' => null, 'updated_at' => '2026-02-25 09:24:25'],
            ['producto_id' => 8, 'sucursal_id' => 1, 'existencias' => 6, 'stock_minimo' => 2, 'stock_maximo' => 20, 'fecha_actualizacion' => '2026-02-24 07:39:15', 'created_at' => null, 'updated_at' => '2026-02-24 13:39:15'],
            ['producto_id' => 9, 'sucursal_id' => 1, 'existencias' => 5, 'stock_minimo' => 2, 'stock_maximo' => 20, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 10, 'sucursal_id' => 1, 'existencias' => 8, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 11, 'sucursal_id' => 1, 'existencias' => 4, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 12, 'sucursal_id' => 1, 'existencias' => 4, 'stock_minimo' => 3, 'stock_maximo' => 20, 'fecha_actualizacion' => '2026-02-25 06:00:52', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 13, 'sucursal_id' => 1, 'existencias' => 2, 'stock_minimo' => 3, 'stock_maximo' => 20, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 14, 'sucursal_id' => 1, 'existencias' => 14, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 15, 'sucursal_id' => 1, 'existencias' => 14, 'stock_minimo' => 10, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-25 06:39:45', 'created_at' => null, 'updated_at' => '2026-02-25 06:39:45'],
            ['producto_id' => 16, 'sucursal_id' => 1, 'existencias' => 12, 'stock_minimo' => 10, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-25 06:32:40', 'created_at' => null, 'updated_at' => '2026-02-25 06:32:40'],
            ['producto_id' => 17, 'sucursal_id' => 1, 'existencias' => 8, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-25 06:32:56', 'created_at' => null, 'updated_at' => '2026-02-25 06:32:56'],
            ['producto_id' => 18, 'sucursal_id' => 1, 'existencias' => 7, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-25 06:31:28', 'created_at' => null, 'updated_at' => '2026-02-25 06:31:28'],
            ['producto_id' => 19, 'sucursal_id' => 1, 'existencias' => 9, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-25 06:33:16', 'created_at' => null, 'updated_at' => '2026-02-25 06:33:16'],
            ['producto_id' => 20, 'sucursal_id' => 1, 'existencias' => 6, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-25 06:32:15', 'created_at' => null, 'updated_at' => '2026-02-25 06:32:15'],
            ['producto_id' => 21, 'sucursal_id' => 1, 'existencias' => 15, 'stock_minimo' => 10, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-25 06:31:46', 'created_at' => null, 'updated_at' => '2026-02-25 06:31:46'],
            ['producto_id' => 22, 'sucursal_id' => 1, 'existencias' => 3, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-21 07:39:31', 'created_at' => null, 'updated_at' => '2026-02-21 13:39:31'],
            ['producto_id' => 23, 'sucursal_id' => 1, 'existencias' => 7, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 24, 'sucursal_id' => 1, 'existencias' => 6, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 25, 'sucursal_id' => 1, 'existencias' => 5, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 26, 'sucursal_id' => 1, 'existencias' => 24, 'stock_minimo' => 3, 'stock_maximo' => 20, 'fecha_actualizacion' => '2026-02-21 22:47:20', 'created_at' => null, 'updated_at' => '2026-02-22 04:47:20'],
            ['producto_id' => 27, 'sucursal_id' => 1, 'existencias' => 54, 'stock_minimo' => 10, 'stock_maximo' => 100, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 28, 'sucursal_id' => 1, 'existencias' => 3, 'stock_minimo' => 3, 'stock_maximo' => 20, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 29, 'sucursal_id' => 1, 'existencias' => 2, 'stock_minimo' => 3, 'stock_maximo' => 20, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 30, 'sucursal_id' => 1, 'existencias' => 3, 'stock_minimo' => 2, 'stock_maximo' => 10, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 31, 'sucursal_id' => 1, 'existencias' => 2, 'stock_minimo' => 2, 'stock_maximo' => 10, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 32, 'sucursal_id' => 1, 'existencias' => 2, 'stock_minimo' => 2, 'stock_maximo' => 10, 'fecha_actualizacion' => '2026-02-24 05:52:13', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 33, 'sucursal_id' => 1, 'existencias' => 2, 'stock_minimo' => 2, 'stock_maximo' => 10, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 34, 'sucursal_id' => 1, 'existencias' => 26, 'stock_minimo' => 5, 'stock_maximo' => 30, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 35, 'sucursal_id' => 1, 'existencias' => 1, 'stock_minimo' => 1, 'stock_maximo' => 5, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 36, 'sucursal_id' => 1, 'existencias' => 1, 'stock_minimo' => 1, 'stock_maximo' => 5, 'fecha_actualizacion' => '2026-02-20 23:01:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 37, 'sucursal_id' => 1, 'existencias' => 17, 'stock_minimo' => 5, 'stock_maximo' => 20, 'fecha_actualizacion' => '2026-02-24 02:59:01', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 38, 'sucursal_id' => 1, 'existencias' => 99, 'stock_minimo' => 1, 'stock_maximo' => 5, 'fecha_actualizacion' => '2026-02-25 03:16:09', 'created_at' => null, 'updated_at' => '2026-02-24 13:39:38'],
            ['producto_id' => 39, 'sucursal_id' => 1, 'existencias' => 21, 'stock_minimo' => 20, 'stock_maximo' => 100, 'fecha_actualizacion' => '2026-02-24 07:10:37', 'created_at' => null, 'updated_at' => '2026-02-22 08:34:35'],
            ['producto_id' => 40, 'sucursal_id' => 1, 'existencias' => 28, 'stock_minimo' => 10, 'stock_maximo' => 50, 'fecha_actualizacion' => '2026-02-24 07:11:01', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 41, 'sucursal_id' => 1, 'existencias' => 300, 'stock_minimo' => 50, 'stock_maximo' => 500, 'fecha_actualizacion' => '2026-02-21 23:22:39', 'created_at' => null, 'updated_at' => '2026-02-22 05:22:39'],
            ['producto_id' => 42, 'sucursal_id' => 1, 'existencias' => 39, 'stock_minimo' => 20, 'stock_maximo' => 100, 'fecha_actualizacion' => '2026-02-25 02:56:45', 'created_at' => null, 'updated_at' => null],
            ['producto_id' => 43, 'sucursal_id' => 1, 'existencias' => 20, 'stock_minimo' => 20, 'stock_maximo' => 100, 'fecha_actualizacion' => '2026-02-23 19:51:17', 'created_at' => null, 'updated_at' => '2026-02-24 01:48:50'],
        ];

        DB::table('producto_sucursal')->insert($stock);
    }
}