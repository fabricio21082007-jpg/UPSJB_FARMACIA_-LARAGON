<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['venta_id', 'medicamento_id', 'cantidad', 'precio_unitario', 'subtotal'])]
class DetalleVenta extends Model
{
    protected function casts(): array
    {
        return [
            'precio_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(Medicamento::class);
    }

    // Método del diagrama: +calcularSubtotal()
    public function calcularSubtotal(): float
    {
        return round($this->cantidad * $this->precio_unitario, 2);
    }

    // Método del diagrama: +validarStock()
    public function validarStock(): bool
    {
        return $this->cantidad <= $this->medicamento->stock;
    }
}