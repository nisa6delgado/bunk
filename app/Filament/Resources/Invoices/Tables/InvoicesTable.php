<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),

                TextColumn::make('amount')
                    ->label('Monto (Dólares)')
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 2);
                    }),

                TextColumn::make('amount_ves')
                    ->label('Monto (Bolívares)')
                    ->state(function ($record) {
                        if (rate()) {
                            return number_format($record->amount * rate(), 2);
                        }

                        return '-';
                    }),

                TextColumn::make('paid')
                    ->label('Pagado')
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 2);
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha y hora')
                    ->datetime('d/m/Y h:i A'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Fecha desde'),
                        DatePicker::make('until')->label('Fecha hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordUrl(null)
            ->recordActions([
                Action::make('view')
                    ->label('Ver factura')
                    ->icon('heroicon-o-eye')
                    ->modalContent(function ($record) {
                        $invoice = $record;
                        return view('invoices.view', compact('invoice'));
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),

                DeleteAction::make()
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
