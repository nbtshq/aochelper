<?php

declare(strict_types=1);

namespace NorthernBytes\AocHelper;

use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Puzzle
{
    use InteractsWithIO;

    // see https://tldp.org/LDP/abs/html/exitcodes.html
    public const SUCCESS = 0;

    public const FAILURE = 1;

    public const INVALID = 2;

    /**
     * Name of the puzzle, as given by AoC.
     */
    protected string $puzzleName = '';

    /**
     * Input data for the puzzle
     */
    protected string $puzzleInput;

    /**
     * Answer for the puzzle (to be set after solving)
     */
    protected string $puzzleAnswer;

    /**
     * Description of the puzzle answer
     */
    protected string $puzzleAnswerDescription = 'Answer';

    public function __construct(?string $input = null)
    {
        if (! is_null($input)) {
            $this->setPuzzleInput($input);
        }

        // This is needed when running tests against solutions, i.e. without actual console I/O
        $this->setOutput(
            new \Illuminate\Console\OutputStyle(
                new \Symfony\Component\Console\Input\StringInput(''),
                new \Symfony\Component\Console\Output\NullOutput
            )
        );
    }

    /**
     * Solve the puzzle.
     *
     * This is where the actual puzzle solving logic goes.
     */
    abstract public function solve(): Puzzle;

    /**
     * Get the name of the puzzle for the day, as given by AoC.
     */
    public function getPuzzleName(): string
    {
        return $this->puzzleName;
    }

    /**
     * Get the input for the puzzle
     */
    public function getPuzzleInput(): string
    {
        return $this->puzzleInput;
    }

    /**
     * Get the answer for the puzzle (available after solving)
     */
    public function getPuzzleAnswer(): string
    {
        return $this->puzzleAnswer;
    }

    /**
     * Get the description of the puzzle answer
     */
    public function getPuzzleAnswerDescription(): string
    {
        return $this->puzzleAnswerDescription;
    }

    /**
     * Set the input data for the puzzle
     */
    public function setPuzzleInput(string $input): Puzzle
    {
        $this->puzzleInput = $input;

        return $this;
    }

    /**
     * Set the answer for the puzzle
     */
    protected function setPuzzleAnswer(string $answer): Puzzle
    {
        $this->puzzleAnswer = $answer;

        return $this;
    }

    public function debug(string $message): void
    {
        $this->output->writeln($message, OutputInterface::VERBOSITY_VERBOSE);
    }
}
