<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Pages\Chats;
use Filament\Support\Colors\Color;
use App\Filament\Pages\TransferFunds;
use Filament\Navigation\NavigationItem;
use Filament\View\PanelsRenderHook;
use App\Http\Middleware\AdminMiddleware;
use Rupadana\ApiService\ApiServicePlugin;
use Filament\Http\Middleware\Authenticate;
use CWSPS154\AppSettings\AppSettingsPlugin;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Monzer\FilamentChatifyIntegration\ChatifyPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;


class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            // ->lable('dashboard')
            ->brandName(fn (): string => appReportSettings()['app_name'])
            ->brandLogo(fn (): string => appReportSettings()['app_dark_theme_logo_url'])
            ->brandLogoHeight('100px')
            ->colors([
                'primary' => "#24aae1",
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->databaseNotifications()

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->plugin(ChatifyPlugin::make()->customPage(Chats::class))
            ->plugin(\RickDBCN\FilamentEmail\FilamentEmail::make())
            ->plugin(
                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable()
            )
            ->plugins([AppSettingsPlugin::make()])
            ->plugins([
                ApiServicePlugin::make()
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
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (): string => view('filament.footer.afghan-cosmos')->render(),
            )
            // ->navigationItems([
            //     NavigationItem::make('Daily Transfer')
            //         ->url(fn(): string => TransferFunds::getUrl())
            //         ->icon('heroicon-o-banknotes')
            //         ->sort(3)
            //         ->visible(fn(): bool => auth()->user()->can('process_transfer')),
            // ])

            ->authMiddleware([
                Authenticate::class,
                AdminMiddleware::class,
            ]);
    }
}
