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
        $supportPhones = array_values(array_filter([
            $settings['support_phone_1'] ?? '',
            $settings['support_phone_2'] ?? '',
            $settings['support_phone_3'] ?? '',
            $settings['support_phone_4'] ?? '',
        ], fn ($phone) => filled($phone)));

        $resolveAssetUrl = function (?string $path, string $fallback): string {
            $candidate = filled($path) ? trim($path) : trim($fallback);

            if (blank($candidate)) {
                return asset('schools/cosmos.png');
            }

            if (\Illuminate\Support\Str::startsWith($candidate, ['http://', 'https://', 'data:', '/'])) {
                return $candidate;
            }

            $normalizedCandidate = ltrim(str_replace(['storage/', 'public/'], '', $candidate), '/');

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($normalizedCandidate)) {
                return asset('storage/' . $normalizedCandidate);
            }

            return asset($candidate);
        };

        return array_merge($settings, [
            'app_name' => trim($settings['app_name'] ?? config('app.name', 'School Management')),
            'app_logo_url' => $resolveAssetUrl($settings['app_logo'] ?? null, 'schools/cosmos.png'),
            'app_dark_theme_logo_url' => $resolveAssetUrl($settings['app_dark_theme_logo'] ?? null, $settings['app_logo'] ?? 'schools/cosmos.png'),
            'favicon_url' => $resolveAssetUrl($settings['favicon'] ?? null, 'favicon.ico'),
            'address' => $settings['address'] ?? 'Kabul, Afghanistan',
            'school_address_line' => $settings['school_address_en'] ?? ($settings['school_address'] ?? ($settings['address'] ?? 'Kabul, Afghanistan')),
            'support_email' => $settings['support_email'] ?? '',
            'support_phone_1' => $settings['support_phone_1'] ?? '',
            'support_phone_2' => $settings['support_phone_2'] ?? '',
            'support_phone_3' => $settings['support_phone_3'] ?? '',
            'support_phone_4' => $settings['support_phone_4'] ?? '',
            'support_phones' => $supportPhones,
            'support_phone_display' => implode(' / ', $supportPhones),
        ]);
    }
}
