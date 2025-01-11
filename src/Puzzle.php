<?php

namespace NorthernBytes\AocHelper;

use Illuminate\Console\Concerns\InteractsWithIO;

abstract class Puzzle
{
    use InteractsWithIO;

    // see https://tldp.org/LDP/abs/html/exitcodes.html
    public const SUCCESS = 0;

    public const FAILURE = 1;

    public const INVALID = 2;

    protected string $puzzleName;

    protected string $puzzleInput;

    protected string $puzzleAnswer;

    protected string $puzzleAnswerDescription = 'Answer';

    abstract public function solve(): int;

    public function setPuzzleInput(string $input): void
    {
        $this->puzzleInput = $input;
    }

    protected function setPuzzleAnswer(string $answer): void
    {
        $this->puzzleAnswer = $answer;
    }

    public function getPuzzleName(): string
    {
        return $this->puzzleName;
    }

    public function getPuzzleAnswer(): string
    {
        return $this->puzzleAnswer;
    }
}
