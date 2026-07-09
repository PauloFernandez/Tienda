<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('admin')
            ->login()
            //Aplicando cambios en la configuracion del panel principal
            ->profile(false)
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Mi perfil')
                                             ->icon('heroicon-o-user-circle')
                                             ->url(fn () => route('profile.show')),
            ])
            ->brandLogo(asset('assets/image/logo.jpg'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('assets/image/logo.jpg'))
            // ->navigationGroups([
            //     'Sistema',
            //     'Empleados',
            //     'Configuracion',
            // ])
            // ->renderHook(
            //     PanelsRenderHook::HEAD_END,
            //     fn() => '<style>
            //             .fi-sidebar-nav{background-color:#be1a1a }
            //         </style>'
            // )
            ->font('Roboto')
            ->colors([
                'primary' => '#0d6efd',
                'secondary' => '#adb5bd',
                'danger' => '#dc3545',
                'success' => '#198754',
                'warnig' => '#ffc107',
                'info' => '#0dcaf0',
            ])
            //fin de cambios que bienen por defecto
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
