<?php

namespace NorthernBytes\AocHelper\Support;

use Illuminate\Validation\ValidationException;

class Input
{
    public static function validate($year, $day, $part = null): array
    {
        if (is_null($year)) {
            $year = self::defaultYear();
        }

        self::validateYear($year);
        self::validateDay($day);
        self::validatePart($part);

        return [
            $year,
            $day,
            $part
        ];
    }

    private static function defaultYear(): string
    {
        return (string) match (date('n')) {
            '12' => date('Y'),
            default => date('Y') - 1,
        };
    }

    private static function validateYear(mixed $year): void
    {
        if ($year < 2015 || $year > date('Y')) {
            $endYear = match (date('n')) {
                '12' => date('Y'),
                default => date('Y') - 1,
            };

            throw ValidationException::withMessages([
                'Year must be between 2015 and ' . $endYear
            ]);
        }
    }

    private static function validateDay($day): void
    {
        if ($day < 1 || $day > 25) {
            throw ValidationException::withMessages([
                'Day must be between 1 and 25'
            ]);
        }
    }

    private static function validatePart(mixed $part): void
    {
        if (!is_null($part)) {
            if ($part < 1 || $part > 2) {
                throw ValidationException::withMessages([
                    'Part must be 1 or 2'
                ]);
            }
        }
    }
}
