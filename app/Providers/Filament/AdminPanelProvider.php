<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\MoneyWidget;
use App\Filament\Widgets\SalesWidget;
use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $favicon = '';
        $brand = '';
        $color = '';

        if (! app()->runningInConsole() && Schema::hasTable('settings')) {
            $favicon = Setting::where('key', 'favicon')->first();
            $favicon = $favicon->value;
            $favicon = '/img/' . $favicon;

            $brand = Setting::where('key', 'brand')->first();
            $brand = $brand->value;
            $brand = '/img/' . $brand;

            $color = Setting::where('key', 'color')->first();
            $color = $color->value;
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->favicon($favicon)
            ->brandLogo($brand)
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::hex($color),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                MoneyWidget::class,
                SalesWidget::class,
                AccountWidget::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Usuarios')
                    ->url('/users')
                    ->icon('heroicon-o-user-group'),

                MenuItem::make()
                    ->label('Configuración')
                    ->url('/setting')
                    ->icon('heroicon-o-cog-8-tooth'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
