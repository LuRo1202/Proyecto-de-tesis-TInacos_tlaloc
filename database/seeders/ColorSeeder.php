<?php
// database/seeders/ColorSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    public function run()
    {
        $colores = [
            ['nombre' => 'Negro', 'codigo_hex' => '#000000', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Blanco', 'codigo_hex' => '#FFFFFF', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Beige', 'codigo_hex' => '#F5F5DC', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Arena', 'codigo_hex' => '#EEDDCC', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Azul Rey', 'codigo_hex' => '#4169E1', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Azul Cielo', 'codigo_hex' => '#87CEEB', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Rojo', 'codigo_hex' => '#FF0000', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Rosa Mexicano', 'codigo_hex' => '#FF69B4', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Rosa claro', 'codigo_hex' => '#FFB6C1', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Morado', 'codigo_hex' => '#800080', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Fucsia', 'codigo_hex' => '#FF00FF', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
        ];

        DB::table('colores')->insert($colores);
    }
}