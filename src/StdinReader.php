<?php

declare(strict_types=1);

namespace NorthernBytes\AocHelper;

use NorthernBytes\AocHelper\Interfaces\PuzzleInputProviderInterface;

class StdinReader implements PuzzleInputProviderInterface
{
    public static function inputAvailable(): bool
    {
        $read = [STDIN];
        $write = [];
        $except = [];

        return (bool) stream_select($read, $write, $except, 0, 0);
    }

    public function getPuzzleInput(int $year, int $day): string
    {
        if (self::inputAvailable()) {
            return stream_get_contents(STDIN);
        }

        return '';
    }
}
