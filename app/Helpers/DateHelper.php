<?php

namespace App\Helpers;

class DateHelper
{
    public static function toShamsi($date)
    {
        if (!$date) {
            return 'N/A';
        }

        // Convert the date to a Carbon instance if it's not already
        $date = \Carbon\Carbon::parse($date);

        // Get the Gregorian date components
        $year = $date->year;
        $month = $date->month;
        $day = $date->day;

        // Convert to Shamsi
        $shamsi = self::gregorianToShamsi($year, $month, $day);

        // Format the date
        return $shamsi['year'] . '/' . $shamsi['month'] . '/' . $shamsi['day'];
    }

    private static function gregorianToShamsi($g_y, $g_m, $g_d)
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);

        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $gd = $g_d - 1;

        $g_day_no = 365 * $gy + self::div($gy + 3, 4) - self::div($gy + 99, 100) + self::div($gy + 375, 400);

        for ($i = 0; $i < $gm; ++$i) {
            $g_day_no += $g_days_in_month[$i];
        }
        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
            $g_day_no++;
        }
        $g_day_no += $gd;

        $j_day_no = $g_day_no - 79;

        $j_np = self::div($j_day_no, 12053);
        $j_day_no = $j_day_no % 12053;

        $jy = 979 + 33 * $j_np + 4 * self::div($j_day_no, 1461);

        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::div($j_day_no - 1, 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) {
            $j_day_no -= $j_days_in_month[$i];
        }
        $jm = $i + 1;
        $jd = $j_day_no + 1;

        return array('year' => $jy, 'month' => $jm, 'day' => $jd);
    }

    private static function div($a, $b)
    {
        return (int)($a / $b);
    }
}
