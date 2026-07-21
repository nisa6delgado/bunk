<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MoneyWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $merchandise = 0;
        $debtors = 0;

        foreach (Product::get() as $product) {
            $merchandise = $merchandise + ($product->purchase_price * $product->quantity);
        }

        foreach (Invoice::get() as $invoice) {
            if ($invoice->amount != $invoice->paid) {
                $debtors = $debtors + ($invoice->amount - $invoice->paid);
            }
        }

        $merchandiseDollars = '';
        $debtorsDollars = '';

        if (rate()) {
            $merchandiseDollars = $merchandise * rate();
            $merchandiseDollars = number_format($merchandiseDollars, 2);
            $merchandiseDollars = 'Bs. ' . $merchandiseDollars;

            $debtorsDollars = $debtors * rate();
            $debtorsDollars = number_format($debtorsDollars, 2);
            $debtorsDollars = 'Bs. ' . $debtorsDollars;
        }

        $merchandise = '$' . number_format($merchandise, 2);
        $debtors = '$' . number_format($debtors, 2);

        $rate = 'Bs. ' . number_format(rate(), 2);

        return [
            Stat::make('Dinero en mercancía', $merchandise)
                ->description($merchandiseDollars),

            Stat::make('Dinero que me deben', $debtors)
                ->description($debtorsDollars),

            Stat::make('Tasa', $rate)
                ->visible(rate() ? true : false),
        ];
    }
}
