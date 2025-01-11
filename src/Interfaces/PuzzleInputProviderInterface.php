<?php

declare(strict_types=1);

namespace NorthernBytes\AocHelper\Interfaces;

interface PuzzleInputProviderInterface
{
    public function getPuzzleInput(int $year, int $day): string;
}
