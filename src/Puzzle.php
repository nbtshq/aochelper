<?php

namespace NorthernBytes\AocHelper;

use Illuminate\Console\Command;

abstract class Puzzle extends Command
{
    protected int $puzzleDay;

    protected int $puzzleYear;

    protected string $puzzleName;

    protected string $puzzleInput;

    protected string $puzzleAnswer;

    protected string $puzzleAnswerDescription = 'Answer';

    public function __construct()
    {
        parent::__construct();
    }

    public function setPuzzleInput(string $input): void
    {
        $this->puzzleInput = $input;
    }

    protected function setPuzzleAnswer(string $answer): void
    {
        $this->puzzleAnswer = $answer;
    }
}
