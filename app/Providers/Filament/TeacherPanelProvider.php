<?php

namespace App\Providers\Filament;

use App\Http\Middleware\TeacherMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Monzer\FilamentChatifyIntegration\ChatifyPlugin;

class TeacherPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('teacher')
            ->path('teacher')

            // Aman Private School Branding
            ->brandName('Aman Private School')
            ->brandLogo(asset('aman-logo.svg'))
            ->brandLogoHeight('64px')
            ->favicon(asset('aman-logo.svg'))

            // Primary Color
            ->colors([
                'primary' => '#24aae1',
            ])

            ->profile()
            ->login()
            ->passwordReset()

            // Resources
            ->discoverResources(
                in: app_path('Filament/Teacher/Resources'),
                for: 'App\\Filament\\Teacher\\Resources'
            )

            // Pages
            ->discoverPages(
                in: app_path('Filament/Teacher/Pages'),
                for: 'App\\Filament\\Teacher\\Pages'
            )

            ->pages([
                Pages\Dashboard::class,
            ])

            // Widgets
            ->discoverWidgets(
                in: app_path('Filament/Teacher/Widgets'),
                for: 'App\\Filament\\Teacher\\Widgets'
            )

            // Middleware
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

            // Chat
            ->plugin(
                ChatifyPlugin::make()
            )

            // Footer
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (): string => view('filament.footer.afghan-cosmos')->render(),
            )

            // Navigation Groups
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('Attendance'))
                    ->icon('heroicon-o-clipboard-document-check'),

                NavigationGroup::make()
                    ->label(fn (): string => __('Homeworks'))
                    ->icon('heroicon-o-paper-clip'),

                NavigationGroup::make()
                    ->label(fn (): string => __('Academic'))
                    ->icon('heroicon-o-book-open'),

                NavigationGroup::make()
                    ->label(fn (): string => __('My Account'))
                    ->icon('heroicon-o-user'),
            ])

            ->sidebarCollapsibleOnDesktop()

            ->databaseNotifications()

            ->authMiddleware([
                Authenticate::class,
                TeacherMiddleware::class,
            ]);
    }
}