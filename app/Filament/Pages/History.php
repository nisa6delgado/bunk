<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class History extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.table';

    protected static ?string $slug = 'history/{product_id}';

    public $product;

    public function mount($product_id)
    {
        $this->product = Product::find($product_id);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getTitle(): string|Htmlable
    {
        return 'Historial de ventas: ' . $this->product->name;
    }
    
    public function table(Table $table): Table
    {
        $products = Invoice::query()
            ->whereLike('products', '%"product":1%')
            ->orderByDesc('id');

        return $table
            ->query($products)
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
                        $index = array_search($this->product->id, array_column($record->products, 'product'));
                        return number_format($record->products[$index]['price'], 2);
                    })
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Fecha y hora')
                    ->datetime('d/m/Y h:i A')
                    ->sortable(),
            ])
            ->actions([

            ]);
    }
}
