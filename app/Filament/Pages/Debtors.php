<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use stdClass;

class Debtors extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.table';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    public static function getNavigationLabel(): string
    {
        return 'Deudores';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Deudores';
    }

    public function table(Table $table): Table
    {
        $debtors = Invoice::query()
            ->whereColumn('amount', '>', 'paid')
            ->orderBy('created_at', 'desc');

        return $table
            ->query($debtors)
            ->columns([
                TextColumn::make('#')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration + ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1))
                        );
                    }
                ),

                TextColumn::make('customer')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->formatStateUsing(function ($state, $record) {
                        return number_format($state - $record->paid, 2);
                    })
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->datetime('d/m/Y h:i A')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Fecha de último pago')
                    ->datetime('d/m/Y h:i A')
                    ->sortable(),
            ])
            ->actions([
                Action::make('pay')
                    ->label('Pagar')
                    ->button()
                    ->modalWidth('xs')
                    ->icon('heroicon-o-banknotes')
                    ->modalButton('Pagar')
                    ->schema([
                        TextInput::make('amount')
                            ->label('Monto')
                            ->numeric()
                            ->default(function ($record) {
                                return $record->amount - $record->paid;
                            })
                            ->maxValue(function ($record) {
                                return $record->amount - $record->paid;
                            })
                            ->required(),
                    ])
                    ->action(function ($data, $record) {
                        $paid = $record->paid + $data['amount'];

                        if ($paid == $record->amount) {
                            $credit = null;
                        } else {
                            $credit = 1;
                        }

                        $record->update([
                            'paid' => $paid,
                            'credit' => $credit,
                        ]);

                        Notification::make()
                            ->title('Pago registrado')
                            ->success()
                            ->send();

                        return redirect('/debtors');
                    })
            ]);
    }
}
