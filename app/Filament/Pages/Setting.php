<?php

namespace App\Filament\Pages;

use App\Models\Setting as Model;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class Setting extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected string $view = 'filament.form';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $name = Model::where('key', 'name')->first();
        $name = $name->value;

        $color = Model::where('key', 'color')->first();
        $color = $color->value;

        $this->form->fill([
            'name' => $name,
            'color' => $color,
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Configuración';
    }

    public function form(Schema $schema): Schema
    {
        $favicon = Model::where('key', 'favicon')->first();
        $favicon = $favicon->value;

        $brand = Model::where('key', 'brand')->first();
        $brand = $brand->value;

        return $schema->schema([
            Grid::make()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->autofocus(),

                    ColorPicker::make('color')
                        ->label('Color')
                        ->required(),
                ]),

            ViewField::make('images')
                ->view('settings.images', compact('favicon', 'brand')),

            FileUpload::make('favicon'),

            FileUpload::make('brand'),
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
        
        Model::updateOrCreate([
            'key' => 'name',
            'value' => $data['name'],
        ]);

        if ($data['logo']) {
            Model::updateOrCreate([
                'key' => 'logo',
                'value' => $data['logo'],
            ]);
        }

        return Notification::make()
            ->title('Configuración actualizada')
            ->success()
            ->send();

        return redirect('/setting');
    }
}
