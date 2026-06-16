<?php

namespace App\Providers\Filament;

use App\Http\Middleware\StudentMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
use Filament\Navigation\NavigationGroup;
use Monzer\FilamentChatifyIntegration\ChatifyPlugin;

class StudentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('student')
            ->path('student')
            ->login()
            ->profile()
            ->brandLogo(asset('logo.png'))
            ->brandLogoHeight('100px')
            ->colors([
                'primary' => "#24aae1",
            ])
            ->discoverResources(in: app_path('Filament/Student/Resources'), for: 'App\\Filament\\Student\\Resources')
            ->discoverPages(in: app_path('Filament/Student/Pages'), for: 'App\\Filament\\Student\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Student/Widgets'), for: 'App\\Filament\\Student\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->passwordReset()
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            // ->brandName('Afghan Cosmos')
            // ->brandLogoHeight('4rem')
            ->sidebarCollapsibleOnDesktop()

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
                // \Hasnayeen\Themes\Http\Middleware\SetTheme::class

            ])
            ->plugins([
                // \Hasnayeen\Themes\ThemesPlugin::make()
                //     ->canViewThemesPage(fn() => false),
            ])
            ->plugin(
                ChatifyPlugin::make()
            )
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (): string => view('filament.footer.afghan-cosmos')->render(),
            )
            ->navigationGroups([

                NavigationGroup::make()
                    ->label(fn(): string => __('Attendance'))
                    ->icon('heroicon-o-document-check')
                    ->collapsed(),

            NavigationGroup::make()
                    ->label(fn(): string => __('Student Leave'))
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),


                NavigationGroup::make()
                    ->label(fn(): string => __('Homeworks'))
                    ->icon('heroicon-o-paper-clip')
                    ->collapsed(),

            ])
            ->spa()
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
                StudentMiddleware::class
            ]);
    }
}
