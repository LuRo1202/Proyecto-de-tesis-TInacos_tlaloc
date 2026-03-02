<?php
// database/seeders/CategoriaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            ['nombre' => 'Tinacos', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Tinacos Bala', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Cisternas', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['nombre' => 'Accesorios', 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-24 01:53:09'],
        ];

        DB::table('categorias')->insert($categorias);
    }
}