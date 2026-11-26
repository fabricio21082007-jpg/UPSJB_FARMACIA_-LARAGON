<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private Collection $ventas)
    {
    }

    public function collection(): Collection
    {
        return $this->ventas;
    }

    public function headings(): array
    {
        return ['N°', 'Fecha', 'Cliente', 'Vendedor', 'IGV (S/)', 'Total (S/)'];
    }

    public function map($venta): array
    {
        return [
            str_pad($venta->id, 6, '0', STR_PAD_LEFT),
            $venta->fecha->format('d/m/Y H:i'),
            $venta->cliente->nombre ?? '-',
            $venta->usuario->name ?? '-',
            round($venta->igv, 2),
            round($venta->total, 2),
        ];
    }
}