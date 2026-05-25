<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $week = 0;
        $biweek = 0;
        $month = 0;

        $startWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endWeek = Carbon::now()->endOfWeek()->format('Y-m-d');

        $startBiweek = Carbon::now()->subDays(15)->format('Y-m-d');

        $startMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        $current = Carbon::now()->format('Y-m-d');

        foreach (Invoice::get() as $invoice) {
            if ($invoice->created_at >= $startWeek && $invoice->created_at <= $endWeek) {
                $week = $week + $invoice->paid;
            }
            
            if ($invoice->created_at >= $startBiweek && $invoice->created_at <= $current) {
                $biweek = $biweek + $invoice->paid;
            }

            if ($invoice->created_at >= $startMonth && $invoice->created_at <= $endMonth) {
                $month = $month + $invoice->paid;
            }
        }

        $week = '$' . number_format($week, 2);
        $biweek = '$' . number_format($biweek, 2);
        $month = '$' . number_format($month, 2);

        return [
            Stat::make('Dinero en ventas esta semana', $week),
            Stat::make('Dinero en ventas los últimos 15 días', $biweek),
            Stat::make('Dinero en ventas este mes', $month),
        ];
    }
}
