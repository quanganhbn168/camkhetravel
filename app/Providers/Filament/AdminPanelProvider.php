<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Settings\WebsiteSettings;
use App\Support\Branding\FaviconService;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\CuratorPlugin;
use Awcodes\Curator\Models\Media;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
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
            ->path('admin')
            ->login()
            ->brandName(fn (): string => app(WebsiteSettings::class)->site_name)
            ->brandLogo(function (): ?string {
                $website = app(WebsiteSettings::class);

                return MediaUrl::versioned(Media::query()->find($website->logo_media_id));
            })
            ->brandLogoHeight('2.5rem')
            ->favicon(fn (): string => app(FaviconService::class)->primaryUrl())
            ->renderHook(PanelsRenderHook::HEAD_END, function (): string {
                return view('filament.partials.favicon', [
                    'faviconLinks' => app(FaviconService::class)->links(),
                ])->render();
            })
            ->colors([
                'primary' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->navigationGroups([
                NavigationGroup::make('Nội dung website'),
                NavigationGroup::make('Landingpage')
                    ->collapsed(),
                NavigationGroup::make('Trang chủ')
                    ->collapsed(),
                NavigationGroup::make('Khách hàng'),
                NavigationGroup::make('SEO & media')
                    ->collapsed(),
                NavigationGroup::make('Hệ thống')
                    ->collapsed(),
                NavigationGroup::make('Cài đặt website')
                    ->collapsed(),
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('Hệ thống')
                    ->navigationSort(10)
                    ->navigationLabel('Vai trò & quyền'),
                CuratorPlugin::make()
                    ->label('Thư viện media')
                    ->pluralLabel('Thư viện media')
                    ->navigationGroup('SEO & media')
                    ->navigationSort(1)
                    ->showBadge(true)
                    ->curations(true)
                    ->fileSwap(true),
            ])
            ->pages([
                AdminDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
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
                Authenticate::class,
            ]);
    }
}
