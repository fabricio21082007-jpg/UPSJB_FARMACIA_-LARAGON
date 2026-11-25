<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Llena la base de datos con datos de prueba: php artisan migrate:fresh --seed
     * El orden importa: cada seeder usa los registros del anterior.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,          // Administrador, Químico farmacéutico y Cajero
            UserSeeder::class,         // un usuario por rol (contraseña: password)
            CategoriaSeeder::class,
            MedicamentoSeeder::class,
            ClienteSeeder::class,
            VentaSeeder::class,        // 60 ventas de los últimos 90 días
        ]);
    }
}
