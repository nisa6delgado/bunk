<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms;
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
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                    
                Forms\Components\Select::make('category_id')
                    ->label('Categoría')
                    ->options($categories)
                    ->default(1)
                    ->required(),

                Forms\Components\TextInput::make('purchase_price')
                    ->label('Precio de compra')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),

                Forms\Components\TextInput::make('selling_price')
                    ->label('Precio de venta')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),

                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                Forms\Components\TextInput::make('low_stock_alert')
                    ->label('Alerta de poca existencia')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }
}
