<?php

namespace NorthernBytes\AocHelper;

use Illuminate\Support\Facades\File;
use NorthernBytes\AocHelper\Interfaces\PuzzleInputProviderInterface;

class PuzzleInputFileReader implements PuzzleInputProviderInterface
{
    public function getPuzzleInput(int $year, int $day): string
    {
        $puzzleInputFile = sprintf(
            '%s/%d_%02d_input.txt',
            storage_path('aoc/input'),
            $year,
            $day
        );

        if (File::exists($puzzleInputFile)) {
            return File::get($puzzleInputFile);
        }

        return '';
    }
}
