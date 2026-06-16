<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Morilog\Jalali\CalendarUtils;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Facades\FilamentAsset;
use OpenSpout\Writer\XLSX\Helper\DateHelper;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Schema::defaultStringLength(191);
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::PAGE_START,
        //     fn(): ViewView => view('gtranslate'),
        // );

        FilamentAsset::register([
            Css::make('filament-rtl', asset('css/filament-artl.css')),
        ], 'filament/rtl-styles');

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'ps', 'fa']) // Enabled languages
                ->labels([
                    'en' => 'English',
                    'ps' => 'پښتو', // Pashto label
                    'fa' => 'دری',  // Dari label
                ]);
        });
        CalendarUtils::useAfghanMonthsName();

        // Register the DateHelper
        $this->app->singleton('dateHelper', function () {
            return new DateHelper();
        });
        Filament::registerRenderHook(
            'scripts.end',
            fn() => view('vendor.filament.components.custom-dari-jalali-locale')
        );

        Table::$defaultDateDisplayFormat = 'Y/m/d';
        Table::$defaultDateTimeDisplayFormat = 'Y/m/d H:i';

        DatePicker::$defaultDateDisplayFormat = 'Y/m/d';
        DateTimePicker::$defaultDateTimeDisplayFormat = 'Y/m/d H:i';
    }
}
