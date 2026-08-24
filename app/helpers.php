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

if (! function_exists('marksToWords')) {
    function marksToWords(int|float|string|null $number): string
    {
        if ($number === null || $number === '') {
            return '';
        }

        if (! is_numeric($number)) {
            return '';
        }

        $number = round((float) $number, 2);

        if ($number < 0) {
            return '';
        }

        $ones = [
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
        ];
        $tens = [
            2 => 'Twenty',
            3 => 'Thirty',
            4 => 'Forty',
            5 => 'Fifty',
            6 => 'Sixty',
            7 => 'Seventy',
            8 => 'Eighty',
            9 => 'Ninety',
        ];

        $toWords = function (int $value) use (&$toWords, $ones, $tens): string {
            if ($value < 20) {
                return $ones[$value];
            }

            if ($value < 100) {
                $remainder = $value % 10;

                return $tens[intdiv($value, 10)] . ($remainder ? ' ' . $ones[$remainder] : '');
            }

            if ($value < 1000) {
                $remainder = $value % 100;

                return $ones[intdiv($value, 100)] . ' Hundred' . ($remainder ? ' ' . $toWords($remainder) : '');
            }

            $remainder = $value % 1000;

            return $toWords(intdiv($value, 1000)) . ' Thousand' . ($remainder ? ' ' . $toWords($remainder) : '');
        };

        $integer = (int) $number;
        $words = $toWords($integer);
        $decimalDigit = (int) round(($number - $integer) * 10);

        if ($decimalDigit > 0) {
            $words .= ' Point ' . $ones[$decimalDigit];
        }

        return $words;
    }
}
