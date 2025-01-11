<?php

declare(strict_types=1);

namespace NorthernBytes\AocHelper\Interfaces;

interface PuzzleAnswerProviderInterface
{
    public function getPuzzleAnswer(int $year, int $day, int $part): string;
}
