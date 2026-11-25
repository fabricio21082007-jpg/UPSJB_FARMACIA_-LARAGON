<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Crea un usuario de prueba por rol (dos cajeros). Todos con la contraseña "password".
     */
    public function run(): void
    {
        // ['Administrador' => 1, 'Químico farmacéutico' => 2, 'Cajero' => 3]
        $roles = Rol::query()->pluck('id', 'nombre');

        $users = [
            [
                'rol' => 'Administrador',
                'name' => 'Administrador',
                'dni' => '40123456',
                'telefono' => '987654321',
                'colegiatura' => null,
                'email' => 'admin@sanjuan.com',
            ],
            [
                'rol' => 'Químico farmacéutico',
                'name' => 'Rosa Quispe Huamán',
                'dni' => '41234567',
                'telefono' => '956123478',
                'colegiatura' => 'CQFP-04587',
                'email' => 'rquispe@sanjuan.com',
            ],
            [
                'rol' => 'Cajero',
                'name' => 'Carlos Mendoza Ríos',
                'dni' => '42345678',
                'telefono' => '945789123',
                'colegiatura' => null,
                'email' => 'cmendoza@sanjuan.com',
            ],
            [
                'rol' => 'Cajero',
                'name' => 'Lucía Torres Salazar',
                'dni' => '43456789',
                'telefono' => '934567812',
                'colegiatura' => null,
                'email' => 'ltorres@sanjuan.com',
            ],
        ];

        foreach ($users as $user) {
            // updateOrCreate: se puede ejecutar varias veces sin chocar con el unique de email/dni
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'rol_id' => $roles[$user['rol']],
                    'name' => $user['name'],
                    'dni' => $user['dni'],
                    'telefono' => $user['telefono'],
                    'colegiatura' => $user['colegiatura'],
                    'password' => 'password', // el cast "hashed" del modelo User la encripta
                ],
            );
        }
    }
}
