<?php

namespace App\Providers\Filament;

use App\Http\Middleware\StudentMiddleware;
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

class StudentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('student')
            ->path('student')
            ->login()
            ->passwordReset()
            ->profile()

            // Aman Private School Branding
            ->brandName('Aman Private School')
            ->brandLogo(asset('aman-logo.svg'))
            ->brandLogoHeight('64px')
            ->favicon(asset('aman-logo.svg'))

            // Primary Color
            ->colors([
                'primary' => '#24aae1',
            ])

            // Resources
            ->discoverResources(
                in: app_path('Filament/Student/Resources'),
                for: 'App\\Filament\\Student\\Resources'
            )

            // Pages
            ->discoverPages(
                in: app_path('Filament/Student/Pages'),
                for: 'App\\Filament\\Student\\Pages'
            )

            ->pages([
                Pages\Dashboard::class,
            ])

            // Widgets
            ->discoverWidgets(
                in: app_path('Filament/Student/Widgets'),
                for: 'App\\Filament\\Student\\Widgets'
            )

            ->widgets([
                \App\Filament\Student\Widgets\AttendanceChart::class,
            ])

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
                    ->label(fn (): string => __('Academic'))
                    ->icon('heroicon-o-book-open'),

                NavigationGroup::make()
                    ->label(fn (): string => __('Attendance'))
                    ->icon('heroicon-o-document-check')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Student Leave'))
                    ->icon('heroicon-o-arrow-right-start-on-rectangle')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Homeworks'))
                    ->icon('heroicon-o-paper-clip'),

                NavigationGroup::make()
                    ->label(fn (): string => __('Finance'))
                    ->icon('heroicon-o-banknotes'),

                NavigationGroup::make()
                    ->label(fn (): string => __('Reception'))
                    ->icon('heroicon-o-inbox'),
            ])

            // SPA
            ->spa()

            // Notifications
            ->databaseNotifications()

            // Sidebar
            ->sidebarCollapsibleOnDesktop()

            // Authentication Middleware
            ->authMiddleware([
                Authenticate::class,
                StudentMiddleware::class,
            ]);
    }
}