<?php

declare(strict_types=1);

namespace App\Services;

/**
 * ponytail: static mapping; upgrade to DB-driven table if custom categories needed.
 */
class DdcLookup
{
    /** @var array<string, string> */
    private static array $classes = [
        '000' => 'Computer Science, Information & General Works',
        '100' => 'Philosophy & Psychology',
        '200' => 'Religion',
        '300' => 'Social Sciences',
        '400' => 'Language',
        '500' => 'Science',
        '600' => 'Technology',
        '700' => 'Arts & Recreation',
        '800' => 'Literature',
        '900' => 'History & Geography',
    ];

    /**
     * Parse a DDC string (e.g. "500.123") into class label, code, and level.
     */
    public static function parse(string $ddc): ?array
    {
        $clean = preg_replace('/[^0-9.]/', '', $ddc);
        if ($clean === '' || $clean === null) {
            return null;
        }

        $parts = explode('.', $clean, 2);
        $hundreds = str_pad($parts[0], 3, '0', \STR_PAD_LEFT);
        $hundreds = substr($hundreds, 0, 3);
        // Round down to nearest hundred
        $classCode = (string) (floor((int) $hundreds / 100) * 100);
        $classCode = str_pad($classCode, 3, '0', \STR_PAD_LEFT);

        $level = match (true) {
            isset($parts[1]) && strlen($parts[1]) >= 3 => 'specific',
            isset($parts[1]) => 'division',
            default => 'class',
        };

        return [
            'raw' => $clean,
            'class_code' => $classCode,
            'class_label' => self::$classes[$classCode] ?? 'Unknown',
            'level' => $level,
            'subject_number' => $parts[0],
            'division_number' => $parts[1] ?? null,
        ];
    }

    public static function classes(): array
    {
        return self::$classes;
    }
}