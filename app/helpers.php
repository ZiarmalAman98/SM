<?php

if (!function_exists('dariMonthName')) {
    function dariMonthName($jalaliDate)
    {
        $dariMonths = [
            'فروردین' => 'حمل',
            'اردیبهشت' => 'ثور',
            'خرداد' => 'جوزا',
            'تیر' => 'سرطان',
            'مرداد' => 'اسد',
            'شهریور' => 'سنبله',
            'مهر' => 'میزان',
            'آبان' => 'عقرب',
            'آذر' => 'قوس',
            'دی' => 'جدی',
            'بهمن' => 'دلو',
            'اسفند' => 'حوت',
        ];

        foreach ($dariMonths as $fa => $ps) {
            $jalaliDate = str_replace($fa, $ps, $jalaliDate);
        }

        return $jalaliDate;
    }
}

if (!function_exists('appReportSettings')) {
    function appReportSettings(): array
    {
        $settings = \App\Models\AppSetting::pluck('value', 'key')->toArray();

        $assetUrl = function (?string $path, string $fallback): string {
            if (blank($path)) {
                return asset($fallback);
            }

            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
                return $path;
            }

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }

            return asset($path);
        };

        return array_merge($settings, [
            'app_name' => $settings['app_name'] ?? config('app.name', 'School Management'),
            'app_logo_url' => $assetUrl($settings['app_logo'] ?? null, 'schools/cosmos.png'),
            'app_dark_theme_logo_url' => $assetUrl($settings['app_dark_theme_logo'] ?? null, $settings['app_logo'] ?? 'schools/cosmos.png'),
            'favicon_url' => $assetUrl($settings['favicon'] ?? null, 'favicon.ico'),
            'address' => $settings['address'] ?? 'Kabul, Afghanistan',
            'support_email' => $settings['support_email'] ?? '',
            'support_phone_1' => $settings['support_phone_1'] ?? '',
            'support_phone_2' => $settings['support_phone_2'] ?? '',
        ]);
    }
}
