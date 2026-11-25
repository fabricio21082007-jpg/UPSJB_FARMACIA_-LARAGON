<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Crea 40 clientes de prueba con datos al azar (Faker en español de Perú).
     */
    public function run(): void
    {
        $faker = fake('es_PE');

        for ($i = 0; $i < 40; $i++) {
            Cliente::query()->create([
                'nombre' => $faker->firstName().' '.$faker->lastName().' '.$faker->lastName(),
                'dni' => $faker->unique()->numerify('########'),   // 8 dígitos, sin repetirse
                'telefono' => $faker->numerify('9########'),       // celular de 9 dígitos
                'edad' => $faker->numberBetween(18, 80),
            ]);
        }
    }
}
