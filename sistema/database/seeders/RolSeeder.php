<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso administrativo al sistema.',
            ],
            [
                'nombre' => 'Químico farmacéutico',
                'descripcion' => 'Rol para pruebas.',
            ],
        ];

        foreach ($roles as $rol) {
            // firstOrCreate: se puede ejecutar varias veces sin chocar con el unique de "nombre"
            Rol::query()->firstOrCreate(
                ['nombre' => $rol['nombre']],
                ['descripcion' => $rol['descripcion']],
            );
        }
    }
}
