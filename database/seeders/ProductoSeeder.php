<?php
// database/seeders/ProductoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        $productos = [
            // Tinacos
            ['codigo' => 'TIN-225', 'nombre' => 'Tinaco 225 lts', 'litros' => 225, 'categoria_id' => 1, 'color_id' => 2, 'precio' => 1500.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-225-C', 'nombre' => 'Tinaco 225 lts', 'litros' => 225, 'categoria_id' => 1, 'color_id' => 3, 'precio' => 1600.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-450', 'nombre' => 'Tinaco 450 lts', 'litros' => 450, 'categoria_id' => 1, 'color_id' => 1, 'precio' => 1850.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-450-C', 'nombre' => 'Tinaco 450 lts', 'litros' => 450, 'categoria_id' => 1, 'color_id' => 3, 'precio' => 1950.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-750', 'nombre' => 'Tinaco 750 lts', 'litros' => 750, 'categoria_id' => 1, 'color_id' => 1, 'precio' => 2450.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-750-C', 'nombre' => 'Tinaco 750 lts', 'litros' => 750, 'categoria_id' => 1, 'color_id' => 3, 'precio' => 2550.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-750-MD', 'nombre' => 'Tinaco 750 lts Mayor diametro', 'litros' => 750, 'categoria_id' => 1, 'color_id' => 1, 'precio' => 2650.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-1100', 'nombre' => 'Tinaco 1,100 lts', 'litros' => 1100, 'categoria_id' => 1, 'color_id' => 1, 'precio' => 3250.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-1100-C', 'nombre' => 'Tinaco 1,100 lts', 'litros' => 1100, 'categoria_id' => 1, 'color_id' => 3, 'precio' => 3350.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-1200', 'nombre' => 'Tinaco 1,200 lts', 'litros' => 1200, 'categoria_id' => 1, 'color_id' => 1, 'precio' => 3450.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-1200-C', 'nombre' => 'Tinaco 1,200 lts', 'litros' => 1200, 'categoria_id' => 1, 'color_id' => 3, 'precio' => 3550.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-1500', 'nombre' => 'Tinaco 1,500 lts', 'litros' => 1500, 'categoria_id' => 1, 'color_id' => 1, 'precio' => 4250.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'TIN-1500-C', 'nombre' => 'Tinaco 1,500 lts', 'litros' => 1500, 'categoria_id' => 1, 'color_id' => 3, 'precio' => 4350.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            
            // Tinacos Bala
            ['codigo' => 'TOL-250', 'nombre' => 'Tolva 250 lts', 'litros' => 250, 'categoria_id' => 2, 'color_id' => 2, 'precio' => 1200.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'DISP-20', 'nombre' => 'Dispensador 20 lts', 'litros' => 20, 'categoria_id' => 2, 'color_id' => 3, 'precio' => 450.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'DISP-20-N', 'nombre' => 'Dispensador 20 lts', 'litros' => 20, 'categoria_id' => 2, 'color_id' => 1, 'precio' => 450.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'DISP-20-R', 'nombre' => 'Dispensador 20 lts', 'litros' => 20, 'categoria_id' => 2, 'color_id' => 7, 'precio' => 450.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'DISP-20-AZ', 'nombre' => 'Dispensador 20 lts', 'litros' => 20, 'categoria_id' => 2, 'color_id' => 5, 'precio' => 450.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'DISP-20-RM', 'nombre' => 'Dispensador 20 lts', 'litros' => 20, 'categoria_id' => 2, 'color_id' => 8, 'precio' => 450.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'DISP-20-M', 'nombre' => 'Dispensador 20 lts', 'litros' => 20, 'categoria_id' => 2, 'color_id' => 10, 'precio' => 450.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'DISP-20-B', 'nombre' => 'Dispensador 20 lts', 'litros' => 20, 'categoria_id' => 2, 'color_id' => 2, 'precio' => 450.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'BALA-450', 'nombre' => 'Tinaco Bala 450 lts', 'litros' => 450, 'categoria_id' => 2, 'color_id' => 2, 'precio' => 2100.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'BALA-450-C', 'nombre' => 'Tinaco Bala 450 lts', 'litros' => 450, 'categoria_id' => 2, 'color_id' => 3, 'precio' => 2200.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'BALA-750', 'nombre' => 'Tinaco Bala 750 lts', 'litros' => 750, 'categoria_id' => 2, 'color_id' => 2, 'precio' => 2750.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'BALA-750-C', 'nombre' => 'Tinaco Bala 750 lts', 'litros' => 750, 'categoria_id' => 2, 'color_id' => 3, 'precio' => 2850.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'BALA-1100', 'nombre' => 'Tinaco Bala 1,100 lts', 'litros' => 1100, 'categoria_id' => 2, 'color_id' => 2, 'precio' => 3600.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'BALA-1100-C', 'nombre' => 'Tinaco Bala 1,100 lts', 'litros' => 1100, 'categoria_id' => 2, 'color_id' => 3, 'precio' => 3700.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'BALA-1900', 'nombre' => 'Tinaco Bala 1,900 lts', 'litros' => 1900, 'categoria_id' => 2, 'color_id' => 2, 'precio' => 5200.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'BALA-1900-C', 'nombre' => 'Tinaco Bala 1,900 lts', 'litros' => 1900, 'categoria_id' => 2, 'color_id' => 3, 'precio' => 5350.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            
            // Cisternas
            ['codigo' => 'CIS-2500', 'nombre' => 'Cisterna 2,500 lts', 'litros' => 2500, 'categoria_id' => 3, 'color_id' => 2, 'precio' => 6850.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'CIS-3000', 'nombre' => 'Cisterna 3,000 lts', 'litros' => 3000, 'categoria_id' => 3, 'color_id' => 2, 'precio' => 8250.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'CIS-3000-C', 'nombre' => 'Cisterna 3,000 lts', 'litros' => 3000, 'categoria_id' => 3, 'color_id' => 5, 'precio' => 8450.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'CIS-3500', 'nombre' => 'Cisterna 3,500 lts', 'litros' => 3500, 'categoria_id' => 3, 'color_id' => 2, 'precio' => 9500.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'CIS-3500-C', 'nombre' => 'Cisterna 3,500 lts', 'litros' => 3500, 'categoria_id' => 3, 'color_id' => 5, 'precio' => 9700.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'CIS-5000', 'nombre' => 'Cisterna 5,000 lts', 'litros' => 5000, 'categoria_id' => 3, 'color_id' => 2, 'precio' => 12500.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'CIS-5000-C', 'nombre' => 'Cisterna 5,000 lts', 'litros' => 5000, 'categoria_id' => 3, 'color_id' => 5, 'precio' => 12800.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'CIS-10000', 'nombre' => 'Cisterna 10,000 lts', 'litros' => 10000, 'categoria_id' => 3, 'color_id' => 2, 'precio' => 22500.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'CIS-10000-C', 'nombre' => 'Cisterna 10,000 lts', 'litros' => 10000, 'categoria_id' => 3, 'color_id' => 5, 'precio' => 23000.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            
            // Accesorios
            ['codigo' => 'ACC-TAPA', 'nombre' => 'Tapa y aro tipo clic', 'litros' => 0, 'categoria_id' => 4, 'color_id' => null, 'precio' => 150.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'ACC-MULTI', 'nombre' => 'Multiconector con llave válvula', 'litros' => 0, 'categoria_id' => 4, 'color_id' => null, 'precio' => 350.00, 'activo' => 1, 'destacado' => 1, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'ACC-BRIDA-1.5', 'nombre' => 'Brida con salida 1 1/2" N.P.T.', 'litros' => 0, 'categoria_id' => 4, 'color_id' => null, 'precio' => 200.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-22 04:54:10'],
            ['codigo' => 'ACC-BRIDA-2', 'nombre' => 'Brida con salida 2" N.P.T.', 'litros' => 0, 'categoria_id' => 4, 'color_id' => null, 'precio' => 220.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
            ['codigo' => 'ACC-BRIDA-3', 'nombre' => 'Brida con salida 3" N.P.T.', 'litros' => 0, 'categoria_id' => 4, 'color_id' => null, 'precio' => 280.00, 'activo' => 1, 'destacado' => 0, 'created_at' => '2026-02-21 03:55:02', 'updated_at' => '2026-02-21 03:55:02'],
        ];

        DB::table('productos')->insert($productos);
    }
}