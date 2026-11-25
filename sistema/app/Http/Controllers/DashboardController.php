<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Medicamento;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── 1. Indicadores (tarjetas)
        $ventasHoy = Venta::whereDate('fecha', today())->sum('total');
        $ventasMes = Venta::whereBetween('fecha', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');
        $clientes  = Cliente::count();
        $stockBajo = Medicamento::where('stock', '<=', MedicamentoController::STOCK_BAJO)->count();

        // ── 2. Ventas de los últimos 7 días (una sola tabla, agrupada por día)
        //    SQL: SELECT DATE(fecha) AS dia, SUM(total) AS total FROM ventas
        //         WHERE fecha >= ? GROUP BY dia ORDER BY dia
        $porDia = Venta::selectRaw('DATE(fecha) as dia, SUM(total) as total')
            ->where('fecha', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('total', 'dia');            // ['2026-11-24' => 85.50, ...]

        // Un día sin ventas no aparece en la consulta: se completa con 0
        $dias = [];
        $totales = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = now()->subDays($i);
            $dias[] = $dia->format('d/m');
            $totales[] = (float) ($porDia[$dia->toDateString()] ?? 0);
        }

        // ── 3. Ventas del mes por categoría (cuatro tablas unidas con JOIN)
        //    SQL: SELECT categorias.nombre, SUM(detalle_ventas.subtotal) AS total
        //         FROM detalle_ventas
        //         JOIN medicamentos ON medicamentos.id = detalle_ventas.medicamento_id
        //         JOIN categorias   ON categorias.id   = medicamentos.categoria_id
        //         JOIN ventas       ON ventas.id       = detalle_ventas.venta_id
        //         WHERE ventas.fecha BETWEEN ? AND ?
        //         GROUP BY categorias.nombre ORDER BY total DESC
        $porCategoria = DB::table('detalle_ventas')
            ->join('medicamentos', 'medicamentos.id', '=', 'detalle_ventas.medicamento_id')
            ->join('categorias', 'categorias.id', '=', 'medicamentos.categoria_id')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->whereBetween('ventas.fecha', [now()->startOfMonth(), now()->endOfMonth()])
            ->select('categorias.nombre', DB::raw('SUM(detalle_ventas.subtotal) as total'))
            ->groupBy('categorias.nombre')
            ->orderByDesc('total')
            ->get()
            // MySQL devuelve el SUM como texto ("85.50"): se pasa a número para el gráfico
            ->map(fn ($fila) => ['nombre' => $fila->nombre, 'total' => (float) $fila->total]);

        // ── 4. Los 5 medicamentos más vendidos del mes (unidades vendidas)
        $topMedicamentos = DB::table('detalle_ventas')
            ->join('medicamentos', 'medicamentos.id', '=', 'detalle_ventas.medicamento_id')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->whereBetween('ventas.fecha', [now()->startOfMonth(), now()->endOfMonth()])
            ->select('medicamentos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as unidades'))
            ->groupBy('medicamentos.nombre')
            ->orderByDesc('unidades')
            ->limit(5)
            ->get()
            ->map(fn ($fila) => ['nombre' => $fila->nombre, 'unidades' => (int) $fila->unidades]);

        return view('principal.dashboard.index', compact(
            'ventasHoy', 'ventasMes', 'clientes', 'stockBajo',
            'dias', 'totales', 'porCategoria', 'topMedicamentos'
        ));
    }
}
