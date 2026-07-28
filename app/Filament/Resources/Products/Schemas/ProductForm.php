<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        $categories = Category::get()
            ->pluck('name', 'id')
            ->toArray();

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                    
                Select::make('category_id')
                    ->label('Categoría')
                    ->options($categories)
                    ->default(1)
                    ->required(),

                TextInput::make('purchase_price')
                    ->label('Precio de compra (Dólares)')
                    ->numeric()
                    ->minValue(0.01),

                TextInput::make('purchase_price_ves')
                    ->label('Precio de compra (Bolívares)')
                    ->numeric()
                    ->minValue(0.01),

                TextInput::make('selling_price')
                    ->label('Precio de venta (Dólares)')
                    ->numeric()
                    ->minValue(0.01),

                TextInput::make('selling_price_ves')
                    ->label('Precio de venta (Bolívares)')
                    ->numeric()
                    ->minValue(0.01),

                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                TextInput::make('low_stock_alert')
                    ->label('Alerta de poca existencia')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }
}
