<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Crea las categorías de medicamentos de la farmacia.
     */
    public function run(): void
    {
        $categorias = [
            'Analgésicos' => 'Alivian el dolor y bajan la fiebre.',
            'Antibióticos' => 'Combaten infecciones causadas por bacterias.',
            'Antialérgicos' => 'Alivian los síntomas de la alergia.',
            'Antigripales' => 'Alivian los síntomas de la gripe y el resfriado.',
            'Antiinflamatorios' => 'Reducen la inflamación y el dolor.',
            'Gastrointestinales' => 'Tratan molestias del estómago y el intestino.',
            'Dermatológicos' => 'Cremas y tratamientos para la piel.',
            'Vitaminas y suplementos' => 'Complementan la alimentación.',
        ];

        foreach ($categorias as $nombre => $descripcion) {
            Categoria::query()->firstOrCreate(['nombre' => $nombre], ['descripcion' => $descripcion]);
        }
    }
}
