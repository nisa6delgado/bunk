<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
            ]);
    }
}
