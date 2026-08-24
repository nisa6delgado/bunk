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
use Illuminate\Support\Facades\Storage;

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

        if (Storage::exists($favicon)) {
            $favicon = asset('storage/' . $favicon);
        } else {
            $favicon = '/img/' . $favicon;
        }

        $brand = Model::where('key', 'brand')->first();
        $brand = $brand->value;

        if (Storage::exists($brand)) {
            $brand = asset('storage/' . $brand);
        } else {
            $brand = '/img/' . $brand;
        }

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

            Grid::make()
                ->columns(2)
                ->schema([
                    ViewField::make('favicon-preview')
                        ->view('settings.image', ['image' => $favicon]),

                    ViewField::make('brand-preview')
                        ->view('settings.image', ['image' => $brand]),
                ]),

            Grid::make()
                ->columns(2)
                ->schema([
                    FileUpload::make('favicon'),

                    FileUpload::make('brand')
                        ->label('Imagen superior'),
                ]),
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
        
        Model::where('key', 'name')
            ->update(['value' => $data['name']]);

        Model::where('key', 'color')
            ->update(['value' => $data['color']]);

        if (isset($data['favicon'])) {
            Model::where('key', 'favicon')
                ->update(['value' => $data['favicon']]);
        }

        if (isset($data['brand'])) {
            Model::where('key', 'brand')
                ->update(['value' => $data['brand']]);
        }

        Notification::make()
            ->title('Configuración actualizada')
            ->success()
            ->send();

        return redirect('/setting');
    }
}
