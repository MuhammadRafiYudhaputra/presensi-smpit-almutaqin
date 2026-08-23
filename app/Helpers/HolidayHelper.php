<?php

namespace App\Helpers;

use Carbon\Carbon;

class HolidayHelper
{
    /**
     * Daftar Hari Libur Nasional Tetap & Bergerak (2025 - 2027)
     */
    protected static $fixedHolidays = [
        '01-01' => 'Tahun Baru Masehi',
        '05-01' => 'Hari Buruh Internasional',
        '06-01' => 'Hari Lahir Pancasila',
        '08-17' => 'Hari Kemerdekaan Republik Indonesia',
        '12-25' => 'Hari Raya Natal',
    ];

    /**
     * Daftar Libur Nasional Khusus / Bergerak per Tahun (Format: Y-m-d)
     */
    protected static $variableHolidays = [
        // 2025
        '2025-01-27' => 'Isra Mi\'raj Nabi Muhammad SAW',
        '2025-01-29' => 'Tahun Baru Imlek 2576 Kongzili',
        '2025-03-29' => 'Hari Suci Nyepi Tahun Baru Saka 1947',
        '2025-03-31' => 'Hari Raya Idul Fitri 1446 H',
        '2025-04-01' => 'Hari Raya Idul Fitri 1446 H',
        '2025-04-18' => 'Wafat Yesus Kristus',
        '2025-04-20' => 'Kebangkitan Yesus Kristus (Paskah)',
        '2025-05-12' => 'Hari Raya Waisak 2569 BE',
        '2025-05-29' => 'Kenaikan Yesus Kristus',
        '2025-06-07' => 'Hari Raya Idul Adha 1446 H',
        '2025-06-27' => '1 Muharam Tahun Baru Islam 1447 H',
        '2025-09-05' => 'Maulid Nabi Muhammad SAW',

        // 2026
        '2026-01-16' => 'Isra Mi\'raj Nabi Muhammad SAW',
        '2026-02-17' => 'Tahun Baru Imlek 2577 Kongzili',
        '2026-03-19' => 'Hari Suci Nyepi Tahun Baru Saka 1948',
        '2026-03-20' => 'Hari Raya Idul Fitri 1447 H',
        '2026-03-21' => 'Hari Raya Idul Fitri 1447 H',
        '2026-04-03' => 'Wafat Yesus Kristus',
        '2026-04-05' => 'Paskah',
        '2026-05-14' => 'Kenaikan Yesus Kristus',
        '2026-05-27' => 'Hari Raya Idul Adha 1447 H',
        '2026-05-31' => 'Hari Raya Waisak 2570 BE',
        '2026-06-16' => 'Tahun Baru Islam 1448 H',
        '2026-08-17' => 'Hari Kemerdekaan Republik Indonesia',
        '2026-08-25' => 'Maulid Nabi Muhammad SAW',

        // 2027
        '2027-01-05' => 'Isra Mi\'raj Nabi Muhammad SAW',
        '2027-02-06' => 'Tahun Baru Imlek 2578 Kongzili',
        '2027-03-09' => 'Hari Raya Idul Fitri 1448 H',
        '2027-03-10' => 'Hari Raya Idul Fitri 1448 H',
        '2027-03-26' => 'Wafat Yesus Kristus',
        '2027-04-07' => 'Hari Suci Nyepi',
        '2027-05-06' => 'Kenaikan Yesus Kristus',
        '2027-05-16' => 'Hari Raya Idul Adha 1448 H',
        '2027-05-20' => 'Hari Raya Waisak 2571 BE',
        '2027-06-06' => 'Tahun Baru Islam 1449 H',
        '2027-08-15' => 'Maulid Nabi Muhammad SAW',
        '2027-08-17' => 'Hari Kemerdekaan Republik Indonesia',
    ];

    /**
     * Cek apakah sebuah tanggal adalah Hari Libur Nasional
     */
    public static function isNationalHoliday($date): bool
    {
        $carbon = is_string($date) ? Carbon::parse($date) : $date;
        $dateString = $carbon->toDateString();
        $monthDay = $carbon->format('m-d');

        if (isset(self::$fixedHolidays[$monthDay])) {
            return true;
        }

        if (isset(self::$variableHolidays[$dateString])) {
            return true;
        }

        return false;
    }

    /**
     * Ambil nama hari libur (jika ada)
     */
    public static function getHolidayName($date): ?string
    {
        $carbon = is_string($date) ? Carbon::parse($date) : $date;
        $dateString = $carbon->toDateString();
        $monthDay = $carbon->format('m-d');

        if (isset(self::$variableHolidays[$dateString])) {
            return self::$variableHolidays[$dateString];
        }

        if (isset(self::$fixedHolidays[$monthDay])) {
            return self::$fixedHolidays[$monthDay];
        }

        return null;
    }

    /**
     * Cek apakah hari adalah hari non-efektif (Weekend atau Libur Nasional)
     */
    public static function isNonEffectiveDay($date): bool
    {
        $carbon = is_string($date) ? Carbon::parse($date) : $date;
        return $carbon->isWeekend() || self::isNationalHoliday($carbon);
    }
}
