<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Pages\Chats;
use Filament\Support\Colors\Color;
use App\Filament\Pages\TransferFunds;
use Filament\Navigation\NavigationGroup;
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

            // Aman Private School Branding
            ->brandName('Aman Private School')
            ->brandLogo(asset('aman-logo.svg'))
            ->brandLogoHeight('64px')
            ->favicon(asset('aman-logo.svg'))

            ->colors([
                'primary' => '#24aae1',
            ])

            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )

            ->discoverClusters(
                in: app_path('Filament/Clusters'),
                for: 'App\\Filament\\Clusters'
            )

            ->pages([
                Pages\Dashboard::class,
            ])

            ->databaseNotifications()

            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )

            ->widgets([
                \App\Filament\Widgets\QuickActions::class,
                \App\Filament\Widgets\RecentPayments::class,
            ])

            ->sidebarCollapsibleOnDesktop()

            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('System'))
                    ->icon('heroicon-o-building-office'),

                NavigationGroup::make()
                    ->label(fn (): string => __('Staff Management'))
                    ->icon('heroicon-o-briefcase'),

                NavigationGroup::make()
                    ->label(fn (): string => __('Account Management'))
                    ->icon('heroicon-o-users')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Student Management'))
                    ->icon('heroicon-o-academic-cap'),

                NavigationGroup::make()
                    ->label(fn (): string => __('Academic Setup'))
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Default Data'))
                    ->icon('heroicon-o-cube')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Attendance'))
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Examinations'))
                    ->icon('heroicon-o-academic-cap')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Assignments'))
                    ->icon('heroicon-o-document-text')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Calendar'))
                    ->icon('heroicon-o-calendar-days')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Evaluations'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Question Bank'))
                    ->icon('heroicon-o-question-mark-circle')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Library Management'))
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Inventory'))
                    ->icon('heroicon-o-archive-box')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Inventory Management'))
                    ->icon('heroicon-o-archive-box')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Employee Allocations'))
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Finance'))
                    ->icon('heroicon-o-banknotes')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Invoices'))
                    ->icon('heroicon-o-receipt-percent')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Payroll'))
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Daily Balance'))
                    ->icon('heroicon-o-arrows-right-left')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Reception'))
                    ->icon('heroicon-o-inbox'),

                NavigationGroup::make()
                    ->label(fn (): string => __('Reports'))
                    ->icon('heroicon-o-chart-pie')
                    ->collapsed(),

                NavigationGroup::make()
                    ->label(fn (): string => __('Admin'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])

            ->plugin(
                ChatifyPlugin::make()
                    ->customPage(Chats::class)
            )

            ->plugin(
                \RickDBCN\FilamentEmail\FilamentEmail::make()
            )

            ->plugin(
                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable()
            )

            ->plugins([
                AppSettingsPlugin::make(),
            ])

            ->plugins([
                ApiServicePlugin::make(),
            ])

            ->plugins([
                FilamentShieldPlugin::make(),
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

            // Footer
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (): string => view('filament.footer.afghan-cosmos')->render(),
            )

            // Aman Private School Login Design
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.aman-login-style')->render(),
            )

            /*
            |--------------------------------------------------------------------------
            | Optional Daily Transfer Navigation
            |--------------------------------------------------------------------------
            |
            | Uncomment when the transfer permission is configured.
            |
            */
            // ->navigationItems([
            //     NavigationItem::make('Daily Transfer')
            //         ->url(fn (): string => TransferFunds::getUrl())
            //         ->icon('heroicon-o-banknotes')
            //         ->sort(3)
            //         ->visible(
            //             fn (): bool => auth()->user()->can('process_transfer')
            //         ),
            // ])

            ->authMiddleware([
                Authenticate::class,
                AdminMiddleware::class,
            ]);
    }
}