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
            <th style="text-align: left">Precio (Bolívares)</th>
            <th style="text-align: left">Total (Dólares)</th>
            <th style="text-align: left">Total (Bolívares)</th>
        </tr>
    </thead>

    <tbody>
        @foreach($invoice->products ?? [] as $item)
            @php
                $product = App\Models\Product::find($item['product']);
                $total = $total ?? 0;
                $total = $total + ($item['price'] * $item['quantity']);
            @endphp

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['price'], 2) }}</td>

                <td>
                    @if ($invoice->paid == $item['price'] && rate($invoice->created_at->format('Y-m-d')))
                        {{ number_format($item['price'] * rate($invoice->created_at->format('Y-m-d')), 2) }}
                    @elseif(rate())
                        {{ number_format($item['price'] * rate(), 2) }}
                    @else
                        -
                    @endif
                </td>

                <td>{{ number_format($item['price'] * $item['quantity'], 2) }}</td>

                <td>
                    @if ($invoice->paid == $item['price'] && rate($invoice->created_at->format('Y-m-d')))
                        {{ number_format(($item['price'] * $item['quantity']) * rate($invoice->created_at->format('Y-m-d')), 2) }}
                    @elseif(rate())
                        {{ number_format(($item['price'] * $item['quantity']) * rate(), 2) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="5"></th>
            <th style="text-align: right">Total (Dólares)</th>
            <th style="text-align: left">{{ number_format($invoice->amount ?? 0, 2) }}</th>
        </tr>

        <tr>
            <th colspan="5"></th>
            <th style="text-align: right">Pagado (Dólares)</th>
            <th style="text-align: left">{{ number_format($invoice->paid ?? 0, 2) }}</th>
        </tr>

        @if($invoice->paid != $invoice->amount)
            <tr>
                <th colspan="5"></th>
                <th style="text-align: right">Restante (Dólares)</th>
                <th style="text-align: left">{{ number_format($invoice->amount - $invoice->paid, 2) }}</th>
            </tr>
        @endif
    </tfoot>
</table>