<?php
// database/seeders/SucursalSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SucursalSeeder extends Seeder
{
    public function run()
    {
        $sucursales = [
            [
                'nombre' => 'Matriz Ecatepec',
                'direccion' => 'Av Morelos Oriente 186 a, Colonia San Cristobal Centro, Ecatepec, Estado de México',
                'telefono' => '55 4017 5803',
                'email' => 'matriz@tanquestlaloc.com',
                'latitud' => 19.60000000,
                'longitud' => -99.04363400,
                'radio_cobertura_km' => 8,
                'activa' => 1,
                'horario' => 'Lunes a Viernes 9:00 - 18:00',
                'created_at' => null,
                'updated_at' => '2026-02-24 02:06:44'
            ],
            [
                'nombre' => 'Sucursal San Luis Potosí',
                'direccion' => 'Francisco I. Madero #492 A, Soledad de Graciano Sánchez, 78437 San Luis Potosí, S.L.P.',
                'telefono' => '444 184 4270',
                'email' => 'sanluis@tanquestlaloc.com',
                'latitud' => 22.15381860,
                'longitud' => -100.92841420,
                'radio_cobertura_km' => 8,
                'activa' => 1,
                'horario' => 'Lunes a Viernes 9:00 - 18:00',
                'created_at' => '2026-02-22 01:07:47',
                'updated_at' => '2026-02-22 01:07:47'
            ],
            [
                'nombre' => 'Sucursal Monterrey',
                'direccion' => 'Av. Lic. Arturo B. de La Garza, poniente, 67267 Nuevo León, N.L.',
                'telefono' => '81 8654 0464',
                'email' => 'monterrey@tanquestlaloc.com',
                'latitud' => 25.64974050,
                'longitud' => -100.10144940,
                'radio_cobertura_km' => 8,
                'activa' => 1,
                'horario' => 'Lunes a Viernes 9:00 - 18:00',
                'created_at' => '2026-02-22 01:07:47',
                'updated_at' => '2026-02-22 01:07:47'
            ]
        ];

        DB::table('sucursales')->insert($sucursales);
    }
}