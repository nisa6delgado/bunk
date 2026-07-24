<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Setting extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected string $view = 'filament.form';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string|Htmlable
    {
        return 'Configuración';
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->autofocus(),
        ])->statePath('data');
    }

    public function getFormActions()
    {
        return [
            Action::make('submit')
                ->submit('submit')
                ->label('Guardar cambios')
                ->icon('heroicon-o-check-circle'),
        ];
    }

    public function submit()
    {
        $data = collect($this->form->getState());
    }
}
