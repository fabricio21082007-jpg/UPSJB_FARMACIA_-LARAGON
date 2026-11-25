<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $role = Rol::query()->firstOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Acceso administrativo al sistema.'],
        );

        $email = 'admin@sanjuan.com';
        $user = User::query()->firstOrNew(['email' => $email]);

        if (! $user->exists) {
            do {
                $dni = (string) random_int(10000000, 99999999);
            } while (User::query()->where('dni', $dni)->exists());

            $user->dni = $dni;
        }

        $user->fill([
            'name' => 'Administrador',
            'rol_id' => $role->id,
            'password' => 'password',
        ]);

        $user->save();
    }
}
