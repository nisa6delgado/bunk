<?php

namespace App\Filament\Pages;

use App\Models\Rate;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\Width;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('rate')
                ->label('Establecer tasa')
                ->icon('heroicon-o-currency-dollar')
                ->modalWidth(Width::Medium)
                ->modalSubmitAction(
                    fn (Action $action) => $action
                        ->label('Establecer')
                        ->icon('heroicon-o-check-circle')
                )
                ->modalCancelAction(
                    fn (Action $action) => $action
                        ->label('Cancelar')
                        ->icon('heroicon-o-x-circle')
                )
                ->form([
                    TextInput::make('rate')
                        ->label('Tasa')
                        ->numeric()
                        ->required()
                ])
                ->action(function ($data) {
                    Rate::create([
                        'value' => $data['rate'],
                    ]);

                    return redirect('/');
                }),
        ];
    }
}
