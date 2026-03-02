<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CategoriaSeeder::class,
            ColorSeeder::class,
            SucursalSeeder::class,
            UsuarioSeeder::class,
            ProductoSeeder::class,
            ProductoSucursalSeeder::class,
        ]);
    }
}