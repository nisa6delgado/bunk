<?php

namespace App\Filament\Pages;

use App\Models\Rate;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    protected $listeners = ['openRateModal' => 'throwModal'];

    public function throwModal(): void
    {
        $this->mountAction('rate');
    }
    
    public function render(): View
    {
        $this->dispatch('openRateModal');
        return parent::render();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('rate')
                ->modalWidth(Width::Medium)
                ->extraAttributes([
                    'class' => 'hidden',
                ])
                ->form([
                    TextInput::make('rate')
                        ->label('Tasa de cambio')
                        ->numeric()
                        ->required(),
                ])
                ->action(function (array $data) {
                    Rate::create($data['rate']);
                }),
        ];
    }
}