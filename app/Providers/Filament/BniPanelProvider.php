<?php

namespace App\Providers\Filament;

use App\Filament\Bni\Pages\ManageBniInvitationSettings;
use App\Filament\Bni\Widgets\BniStatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class BniPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('bni')
            ->path('bni-admin')
            ->login()
            ->brandName('BNI Lễ chuyển giao')
            ->brandLogo(asset('bni-logo-red.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('bni-logo-red.svg'))
            ->colors(['primary' => Color::Rose])
            ->darkMode(false)
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Bni/Resources'), for: 'App\\Filament\\Bni\\Resources')
            ->navigationGroups([
                NavigationGroup::make('Lễ chuyển giao'),
                NavigationGroup::make('Cộng đồng & vận hành'),
            ])
            ->pages([
                Dashboard::class,
                ManageBniInvitationSettings::class,
            ])
            ->widgets([BniStatsOverview::class])
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
            ->authMiddleware([Authenticate::class]);
    }
}
