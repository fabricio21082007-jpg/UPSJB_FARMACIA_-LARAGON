<?php

namespace App\Http\Controllers;

use App\Exports\VentasExport;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function ventas(Request $request)
    {
        [$ventas, $desde, $hasta] = $this->filtrarVentas($request);

        $resumen = $this->resumen($ventas);

        // Datos para el gráfico: total por día dentro del rango
        $grafico = $ventas->groupBy(fn ($venta) => $venta->fecha->format('d/m'))
            ->map(fn ($grupo) => round($grupo->sum('total'), 2));

        // Listas para los <select> de los filtros
        $clientes = Cliente::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('principal.reportes.ventas', compact(
            'ventas', 'resumen', 'grafico', 'desde', 'hasta', 'clientes', 'usuarios', 'categorias'
        ));
    }

    public function ventasPdf(Request $request)
    {
        [$ventas, $desde, $hasta] = $this->filtrarVentas($request);
        $resumen = $this->resumen($ventas);

        $pdf = Pdf::loadView('principal.reportes.ventas-pdf', compact('ventas', 'resumen', 'desde', 'hasta'))
            ->setPaper('a4');

        return $pdf->download("reporte-ventas_{$desde}_a_{$hasta}.pdf");
    }

    public function ventasExcel(Request $request)
    {
        [$ventas, $desde, $hasta] = $this->filtrarVentas($request);

        return Excel::download(new VentasExport($ventas), "reporte-ventas_{$desde}_a_{$hasta}.xlsx");
    }

    /**
     * Valida los filtros y devuelve [ventas, desde, hasta]. Lo usan la vista, el PDF y el Excel.
     */
    private function filtrarVentas(Request $request): array
    {
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
            'cliente_id' => 'nullable|exists:clientes,id',
            'user_id' => 'nullable|exists:users,id',
            'categoria_id' => 'nullable|exists:categorias,id',
        ]);

        $desde = $request->input('desde') ?: now()->startOfMonth()->toDateString();
        $hasta = $request->input('hasta') ?: now()->toDateString();

        $ventas = Venta::with(['cliente', 'usuario'])
            ->whereBetween('fecha', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->when($request->cliente_id, fn ($q, $id) => $q->where('cliente_id', $id))
            ->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id))
            ->when($request->categoria_id, fn ($q, $id) =>
                $q->whereHas('detalles.medicamento', fn ($m) => $m->where('categoria_id', $id)))
            ->orderBy('fecha')
            ->get();

        return [$ventas, $desde, $hasta];
    }

    private function resumen(Collection $ventas): array
    {
        return [
            'cantidad' => $ventas->count(),
            'total' => $ventas->sum('total'),
            'igv' => $ventas->sum('igv'),
            'promedio' => $ventas->avg('total') ?? 0,
        ];
    }
}