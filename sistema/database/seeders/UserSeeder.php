<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ['Administrador' => 1, 'Químico farmacéutico' => 2, ...]
        $roles = Rol::query()->pluck('id', 'nombre');

        $users = [
            [
                'rol' => 'Administrador',
                'name' => 'Juan Perez',
                'dni' => '12345678',
                'telefono' => '987654321',
                'colegiatura' => null,
                'email' => 'juan@sanjuan.com',
            ],
            [
                'rol' => 'Químico farmacéutico',
                'name' => 'Maria Lopez',
                'dni' => '87654321',
                'telefono' => '123456789',
                'colegiatura' => null,
                'email' => 'maria@sanjuan.com',
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
