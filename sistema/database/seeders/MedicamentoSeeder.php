<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Medicamento;
use Illuminate\Database\Seeder;

class MedicamentoSeeder extends Seeder
{
    /**
     * Crea los medicamentos con su categoría, precio (incluye IGV) y stock.
     * Algunos empiezan con poco stock para que se vean en rojo y en el dashboard.
     */
    public function run(): void
    {
        // ['Analgésicos' => 1, 'Antibióticos' => 2, ...]
        $categorias = Categoria::query()->pluck('id', 'nombre');

        // [nombre, categoría, precio, stock]
        $medicamentos = [
            ['Paracetamol 500 mg x 10 tab', 'Analgésicos', 2.50, 180],
            ['Panadol Antigripal x 12 tab', 'Antigripales', 9.90, 90],
            ['Ibuprofeno 400 mg x 10 tab', 'Antiinflamatorios', 3.80, 160],
            ['Naproxeno 550 mg x 10 tab', 'Antiinflamatorios', 6.50, 70],
            ['Diclofenaco gel 1% 50 g', 'Antiinflamatorios', 12.90, 8],
            ['Aspirina 100 mg x 30 tab', 'Analgésicos', 7.20, 85],
            ['Amoxicilina 500 mg x 10 cap', 'Antibióticos', 8.50, 120],
            ['Azitromicina 500 mg x 3 tab', 'Antibióticos', 14.90, 60],
            ['Ciprofloxacino 500 mg x 10 tab', 'Antibióticos', 11.50, 6],
            ['Loratadina 10 mg x 10 tab', 'Antialérgicos', 4.20, 140],
            ['Cetirizina 10 mg x 10 tab', 'Antialérgicos', 5.60, 75],
            ['Clorfenamina 4 mg x 20 tab', 'Antialérgicos', 2.80, 150],
            ['Jarabe para la tos 120 ml', 'Antigripales', 15.50, 65],
            ['Omeprazol 20 mg x 14 cap', 'Gastrointestinales', 6.90, 110],
            ['Sales de rehidratación oral x 1 sobre', 'Gastrointestinales', 1.80, 130],
            ['Loperamida 2 mg x 6 tab', 'Gastrointestinales', 3.50, 9],
            ['Clotrimazol crema 1% 20 g', 'Dermatológicos', 8.90, 45],
            ['Bloqueador solar FPS 50 120 ml', 'Dermatológicos', 39.90, 30],
            ['Calcio + Vitamina D x 30 tab', 'Vitaminas y suplementos', 24.90, 100],
            ['Sulfato ferroso 300 mg x 30 tab', 'Vitaminas y suplementos', 9.50, 120],
            ['Vitamina C 1 g x 10 tab efervescentes', 'Vitaminas y suplementos', 12.50, 95],
        ];

        foreach ($medicamentos as [$nombre, $categoria, $precio, $stock]) {
            Medicamento::query()->firstOrCreate(
                ['nombre' => $nombre],
                ['categoria_id' => $categorias[$categoria], 'precio' => $precio, 'stock' => $stock],
            );
        }
    }
}
