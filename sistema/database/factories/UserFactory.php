<?php

namespace Database\Factories;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Solo se ejecuta si no pasas 'rol_id' en create([...]).
            // Usa el rol Administrador (lo crea si la tabla roles está vacía, p. ej. en tests).
            'rol_id' => fn () => Rol::query()->firstOrCreate(
                ['nombre' => 'Administrador'],
                ['descripcion' => 'Acceso administrativo al sistema.'],
            )->id,
            'name' => fake()->name(),
            'dni' => fake()->unique()->numerify('########'),
            'telefono' => fake()->numerify('9########'),
            'colegiatura' => null,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
