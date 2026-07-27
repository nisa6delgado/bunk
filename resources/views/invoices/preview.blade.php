<style>
    table {
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #c0c0c0;
        border-radius: 10px;
        overflow: hidden;
    }

    td, th {
        padding: 10px;
    }

    tr:hover {
        background-color: #f0eeee;
    }
</style>

<table>
    <thead>
        <tr>
            <th style="text-align: left">#</th>
            <th style="text-align: left">Producto</th>
            <th style="text-align: left">Cantidad</th>
            <th style="text-align: left">Precio (Dólares)</th>

            @if(rate())
                <th style="text-align: left">Precio (Bolívares)</th>
            @endif

            <th style="text-align: left">Total (Dólares)</th>

            @if(rate())
                <th style="text-align: left">Total (Bolívares)</th>
            @endif

            <th></th>
        </tr>
    </thead>

    <tbody>
        @foreach(session()->get('products') ?? [] as $item)
            @php
                $product = App\Models\Product::find($item['product']);
                $total = $total ?? 0;
                $total = $total + ($product->selling_price * $item['quantity']);
            @endphp

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($product->selling_price, 2) }}</td>

                @if(rate())
                    <td>{{ number_format($product->selling_price * rate(), 2) }}</td>
                @endif

                <td>{{ number_format($product->selling_price * $item['quantity'], 2) }}</td>

                @if(rate())
                    <td>{{ number_format($product->selling_price * $item['quantity'] * rate(), 2) }}</td>
                @endif

                <td style="text-align: right">
                    <button
                        wire:click="delete({{ $loop->index }})"
                        wire:confirm="¿Estás seguro que deseas eliminar?"
                        type="button"
                        style="background-color: red; color: white; border-radius: 5px; padding: 5px">
                        Eliminar
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="5"></th>
            <th style="text-align: right">
                ¿Venta a crédito? <input wire:model.live="credit" type="checkbox">
            </th>
        </tr>

        @if($this->credit)
            <tr>
                <th colspan="5"></th>
                <th style="text-align: right">
                    <div>
                        Nombre del cliente: <input wire:model="customer" type="text" style="border: 1px solid silver; border-radius: 5px; padding: 5px">
                    </div>

                    <div style="margin-top: 10px">
                        Abono: <input wire:model="paid" type="text" style="border: 1px solid silver; border-radius: 5px; padding: 5px">
                    </div>
                </th>
            </tr>
        @endif

        <tr>
            <th colspan="4"></th>
            <th style="text-align: left">{{ number_format($total ?? 0, 2) }}</th>
            <th style="text-align: right">
                <button
                    wire:click="save"
                    wire:confirm="¿Estás seguro que deseas finalizar?"
                    type="button"
                    style="background-color: red; color: white; border-radius: 5px; padding: 5px">
                    Finalizar factura
                </button>
            </th>
        </tr>
    </tfoot>
</table>