<?php

namespace App\Providers\Filament;

use App\Filament\Pages\MyProjects;
use App\Filament\Resources\Projects\ProjectResource;
use App\Http\Middleware\AuditAuthenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('app')
            ->login()
            ->homeUrl(fn (): string => auth()->user()?->is_admin ? ProjectResource::getUrl() : MyProjects::getUrl())
            ->colors([
                // Keep the interaction colour close to the navy used in the
                // Vaurentis website, rather than letting Filament derive a
                // much lighter palette from one hexadecimal value.
                'primary' => [
                    50 => '#f4f6fa',
                    100 => '#e6eaf2',
                    200 => '#cbd3e1',
                    300 => '#aab7cc',
                    400 => '#7183a3',
                    500 => '#2c3d5f',
                    600 => '#1f2b45',
                    700 => '#182238',
                    800 => '#121a2d',
                    900 => '#0d1423',
                    950 => '#070c16',
                ],
            ])
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_START, fn (): View => view('filament.components.project-folder'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ->authMiddleware([
                AuditAuthenticate::class,
            ]);
    }
}
