<?php
// database/seeders/UsuarioSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $usuarios = [
            [
                'usuario' => 'admin',
                'contrasena_hash' => Hash::make('password'),
                'nombre' => 'Rogelio Lucas Cristobal',
                'email' => 'admin@tinacos.com',
                'rol' => 'admin',
                'activo' => 1,
                'remember_token' => null,
                'fecha_creacion' => '2026-02-21 18:06:05',
                'created_at' => '2026-02-22 00:06:05',
                'updated_at' => '2026-02-24 05:50:31'
            ],
            [
                'usuario' => 'gerente',
                'contrasena_hash' => Hash::make('password'),
                'nombre' => 'Carlos Martínez',
                'email' => 'gerente@tinacos.com',
                'rol' => 'gerente',
                'activo' => 1,
                'remember_token' => null,
                'fecha_creacion' => '2026-02-21 22:01:16',
                'created_at' => '2026-02-22 04:01:16',
                'updated_at' => '2026-02-24 02:32:13'
            ],
            [
                'usuario' => 'vendedor.ecatepec',
                'contrasena_hash' => Hash::make('password'),
                'nombre' => 'Ana López',
                'email' => 'ana.lopez@tinacos.com',
                'rol' => 'vendedor',
                'activo' => 1,
                'remember_token' => null,
                'fecha_creacion' => '2026-02-21 22:01:17',
                'created_at' => '2026-02-22 04:01:17',
                'updated_at' => '2026-02-24 23:32:35'
            ],
            [
                'usuario' => 'vendedor.slp',
                'contrasena_hash' => Hash::make('password'),
                'nombre' => 'Roberto Sánchez',
                'email' => 'roberto.sanchez@tinacos.com',
                'rol' => 'vendedor',
                'activo' => 1,
                'remember_token' => null,
                'fecha_creacion' => '2026-02-21 22:01:18',
                'created_at' => '2026-02-22 04:01:18',
                'updated_at' => '2026-02-24 14:31:11'
            ]
        ];

        DB::table('usuarios')->insert($usuarios);

        // Relaciones usuario_sucursal
        $relaciones = [
            ['usuario_id' => 2, 'sucursal_id' => 1, 'fecha_asignacion' => '2026-02-21 22:05:31', 'created_at' => '2026-02-22 04:05:31', 'updated_at' => '2026-02-22 04:05:31'],
            ['usuario_id' => 4, 'sucursal_id' => 1, 'fecha_asignacion' => '2026-02-21 22:05:31', 'created_at' => '2026-02-22 04:05:31', 'updated_at' => '2026-02-22 04:05:31'],
            ['usuario_id' => 3, 'sucursal_id' => 1, 'fecha_asignacion' => '2026-02-24 17:32:35', 'created_at' => '2026-02-24 23:32:35', 'updated_at' => '2026-02-24 23:32:35'],
        ];

        DB::table('usuario_sucursal')->insert($relaciones);
    }
}