<?php

namespace App\Exports\Support;

/**
 * Renders Gregorian dates with Khmer numerals and month names for the printable Excel report headers.
 */
class KhmerNumeral
{
    /**
     * @var array<string, string>
     */
    private const DIGITS = [
        '0' => '០', '1' => '១', '2' => '២', '3' => '៣', '4' => '៤',
        '5' => '៥', '6' => '៦', '7' => '៧', '8' => '៨', '9' => '៩',
    ];

    /**
     * @var array<string, string>
     */
    private const MONTHS = [
        '1' => 'មករា', '2' => 'កុម្ភៈ', '3' => 'មីនា', '4' => 'មេសា',
        '5' => 'ឧសភា', '6' => 'មិថុនា', '7' => 'កក្កដា', '8' => 'សីហា',
        '9' => 'កញ្ញា', '10' => 'តុលា', '11' => 'វិច្ឆិកា', '12' => 'ធ្នូ',
    ];

    public static function month(int|string $month): string
    {
        return self::MONTHS[ltrim((string) $month, '0') ?: '0'] ?? '';
    }

    public static function digits(int|string $value): string
    {
        return collect(str_split((string) $value))
            ->map(fn (string $char): string => self::DIGITS[$char] ?? $char)
            ->implode('');
    }
}
