<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration + ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1))
                        );
                    }
                ),
                
                TextColumn::make('name')
                    ->label('Nombre')
                    ->html()
                    ->formatStateUsing(function (string $state, $record) {
                        return $record->quantity <= $record->low_stock_alert
                            ? "<div>$state</div><div style='color: red'>Agotado</div>"
                            : "<div>$state</div>";
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('selling_price')
                    ->label('Precio')
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 2);
                    })
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->sortable(),
            ])
            ->defaultSort(fn (Builder $query) => 
                $query->orderBy(DB::raw('quantity = 0'), 'desc')
                    ->orderBy('id', 'desc')
            )
            ->filters([
                SelectFilter::make('category_id')
                    ->options(Category::get()->pluck('name', 'id')->toArray()),
            ])
            ->recordActions([
                Action::make('invoice')
                    ->label('Agregar a factura')
                    ->button()
                    ->modalWidth('xs')
                    ->schema([
                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(function ($record) {
                                return $record->quantity;
                            })
                            ->required(),
                    ])
                    ->modalButton('Agregar')
                    ->action(function ($data, $record, $livewire) {
                        $products = session()->get('products') ?? [];

                        $products[] = [
                            'product' => $record->id,
                            'price' => $record->selling_price,
                            'quantity' => $data['quantity'],
                        ];

                        session()->put('products', $products);

                        Notification::make()
                            ->title('Producto agregado a factura')
                            ->success()
                            ->send();

                        return redirect('/products');
                    })
                    ->color('info')
                    ->icon('heroicon-o-clipboard-document-check'),

                Action::make('quantity')
                    ->label('Agregar Cantidad')
                    ->modalWidth('xs')
                    ->schema([
                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->modalButton('Agregar')
                    ->action(function ($data, $record) {
                        $quantity = $record->quantity + $data['quantity'];
                        $record->update(['quantity' => $quantity]);

                        Notification::make()
                            ->title('Cantidad agregada')
                            ->success()
                            ->send();
                    })
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-adjustments-horizontal'),

                ActionGroup::make([
                    Action::make('history')
                        ->icon('heroicon-o-list-bullet')
                        ->label('Ver historial')
                        ->url(function ($record) {
                            return '/history/' . $record->id;
                        }),

                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
