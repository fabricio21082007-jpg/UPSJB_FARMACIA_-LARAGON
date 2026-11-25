<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Medicamento;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class VentaSeeder extends Seeder
{
    /**
     * Crea 60 ventas al azar de los últimos 90 días, con las mismas reglas de VentaController@store:
     * el precio sale de la base de datos, se descuenta el stock y el total separa el IGV.
     */
    public function run(): void
    {
        $clientes = Cliente::query()->pluck('id');
        $vendedores = User::query()->pluck('id');

        // 1. Las 60 fechas: unas de hoy y de esta semana (para el dashboard) y el resto de los últimos 90 días
        $fechas = collect(range(1, 60))->map(function (int $n) {
            if ($n <= 4) {
                return $this->fechaEntre(now()->startOfDay(), now());              // hoy
            }
            if ($n <= 15) {
                return $this->fechaEntre(now()->subDays(6)->startOfDay(), now());  // últimos 7 días
            }

            return $this->fechaEntre(now()->subDays(89)->startOfDay(), now());     // últimos 90 días
        })->sort()->values();   // en orden: así el número de venta crece con la fecha

        foreach ($fechas as $fecha) {
            // Medicamentos que todavía tienen stock (se vuelve a leer en cada venta)
            $disponibles = Medicamento::query()->where('stock', '>', 0)->get();

            if ($disponibles->isEmpty()) {
                break;
            }

            // 2. La venta (el total se calcula al final, como en el controlador)
            $venta = Venta::query()->create([
                'cliente_id' => $clientes->random(),
                'user_id' => $vendedores->random(),
                'fecha' => $fecha,
                'total' => 0,
                'igv' => 0,
            ]);

            // 3. De 1 a 4 medicamentos distintos por venta
            foreach ($disponibles->random(min(rand(1, 4), $disponibles->count())) as $medicamento) {
                $cantidad = min(rand(1, 5), $medicamento->stock);

                $venta->detalles()->create([
                    'medicamento_id' => $medicamento->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $medicamento->precio,
                    'subtotal' => round($medicamento->precio * $cantidad, 2),
                ]);

                $medicamento->actualizarStock($cantidad);   // descuenta el stock
            }

            // 4. Total e IGV (el precio ya incluye el 18 % de IGV)
            $venta->calcularTotal();
            $venta->save();
        }
    }

    // Fecha y hora al azar entre dos momentos
    private function fechaEntre(Carbon $desde, Carbon $hasta): Carbon
    {
        return Carbon::createFromTimestamp(rand($desde->timestamp, $hasta->timestamp), $desde->timezone);
    }
}
