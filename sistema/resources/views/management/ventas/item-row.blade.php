{{-- Una fila del detalle de venta.
     items[0][medicamento_id], items[1][medicamento_id]... llegan al controlador como un arreglo $request->items.
     data-price guarda el precio para que JavaScript calcule el subtotal en pantalla. --}}
<tr>
    <td>
        <select name="items[{{ $index }}][medicamento_id]" class="form-select form-select-sm">
            <option value="" data-price="0">-- Seleccione --</option>
            @foreach ($medicamentos as $medicamento)
                <option value="{{ $medicamento->id }}" data-price="{{ $medicamento->precio }}"
                    @selected(($item['medicamento_id'] ?? '') == $medicamento->id)>
                    {{ $medicamento->nombre }} (stock: {{ $medicamento->stock }})
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][cantidad]" min="1"
            class="form-control form-control-sm text-center" value="{{ $item['cantidad'] ?? 1 }}">
    </td>
    <td class="text-end price">S/ 0.00</td>
    <td class="text-end subtotal">S/ 0.00</td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Quitar">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
