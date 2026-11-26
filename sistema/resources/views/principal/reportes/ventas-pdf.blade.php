<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .sub { color: #666; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #ff0844; color: #fff; text-align: left; padding: 6px; }
        td { padding: 5px 6px; border-bottom: 1px solid #ddd; }
        .der { text-align: right; }
        .resumen td { border: none; padding: 2px 6px; }
        tfoot td { font-weight: bold; border-top: 2px solid #222; }
    </style>
</head>
<body>
    <h1>San Juan - Reporte de ventas</h1>
    <div class="sub">
        Del {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
        al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
    </div>

    <table class="resumen" style="width: 60%; margin-bottom: 14px;">
        <tr>
            <td>N.° de ventas: <strong>{{ $resumen['cantidad'] }}</strong></td>
            <td>Total: <strong>S/ {{ number_format($resumen['total'], 2) }}</strong></td>
        </tr>
        <tr>
            <td>IGV: <strong>S/ {{ number_format($resumen['igv'], 2) }}</strong></td>
            <td>Promedio: <strong>S/ {{ number_format($resumen['promedio'], 2) }}</strong></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th class="der">IGV</th>
                <th class="der">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ventas as $venta)
                <tr>
                    <td>{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $venta->fecha->format('d/m/Y H:i') }}</td>
                    <td>{{ $venta->cliente->nombre ?? '-' }}</td>
                    <td>{{ $venta->usuario->name ?? '-' }}</td>
                    <td class="der">S/ {{ number_format($venta->igv, 2) }}</td>
                    <td class="der">S/ {{ number_format($venta->total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No hay ventas en este rango.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="der">Totales</td>
                <td class="der">S/ {{ number_format($resumen['igv'], 2) }}</td>
                <td class="der">S/ {{ number_format($resumen['total'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>