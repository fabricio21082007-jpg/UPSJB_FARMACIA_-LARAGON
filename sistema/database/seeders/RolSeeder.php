<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Crea los tres roles del sistema.
     * Los nombres deben ser exactos (con tilde): se usan en web.php y en el menú.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema.',
            ],
            [
                'nombre' => 'Químico farmacéutico',
                'descripcion' => 'Controla los medicamentos, registra ventas y revisa los reportes.',
            ],
            [
                'nombre' => 'Cajero',
                'descripcion' => 'Registra clientes y ventas.',
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
