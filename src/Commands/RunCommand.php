<?php

declare(strict_types=1);

namespace NorthernBytes\AocHelper\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use NorthernBytes\AocHelper\Interfaces\PuzzleAnswerProviderInterface;
use NorthernBytes\AocHelper\Interfaces\PuzzleInputProviderInterface;
use NorthernBytes\AocHelper\Puzzle;
use NorthernBytes\AocHelper\PuzzleInputFileReader;
use NorthernBytes\AocHelper\Support\AocdWrapper;

use function Termwind\render;

class RunCommand extends Command
{
    public $signature = 'aoc:run { day } { part } { --year= }';

    public $description = 'bar';

    public function handle(): int
    {
        $day = $this->argument('day');
        $part = $this->argument('part');
        $year = $this->option('year') ?: date('Y');

        // Instantiate a class for the correct puzzle
        $namespace = Str::of(config('aochelper.solution.path'))
            ->replace('/', '\\')
            ->ucfirst();
        $className = sprintf('%s\\Year%s\\Day%sPart%s', $namespace, $year, $day, $part);
        if (! class_exists($className)) {
            $this->components->error("No task implementation found for year {$year} day {$day} part {$part}");

            return self::FAILURE;
        }

        /** @var Puzzle $solution */
        $solution = new $className;

        // These are needed for console I/O
        $solution->setOutput($this->output);
        $solution->setInput($this->input);

        // Print puzzle banner
        $this->announcePuzzle($year, $day, $part, $solution->getPuzzleName());

        // Read puzzle input from file

        /** @var PuzzleInputProviderInterface $inputProvider */
        $inputProvider = null;

        // TODO: This is a poc, should really happen somewhere else
        if (config('aochelper.aocdwrapper.enable')) {
            $inputProvider = new AocdWrapper;
        } else {
            $inputProvider = new PuzzleInputFileReader;
        }

        $puzzleInput = trim($inputProvider->getPuzzleInput((int) $year, (int) $day));
        if (empty($puzzleInput)) {
            $this->components->error("No puzzle input available for year {$year} day {$day}");

            return self::FAILURE;
        }

        $solution->setPuzzleInput($puzzleInput);

        // Run the actual solution
        $solution->solve();

        // Print the answer from the solution
        $answerDescription = $solution->getPuzzleAnswerDescription();
        $answer = $solution->getPuzzleAnswer();

        render(<<<HTML
            <div><i>{$answerDescription}:</i> <b><u>{$answer}</u></b></div>
        HTML);

        $this->newLine();

        // Check the answer against previously stored answer, if one exists

        /** @var ?PuzzleAnswerProviderInterface $answerProvider */
        $answerProvider = null;

        // TODO: This is a poc, should really happen somewhere else
        if (config('aochelper.aocdwrapper.enable')) {
            $answerProvider = new AocdWrapper;
        } else {
            // TODO: provide other options for getting stored answers
        }

        $storedAnswer = '';
        if (! is_null($answerProvider)) {
            $storedAnswer = trim($answerProvider->getPuzzleAnswer((int) $year, (int) $day, (int) $part));
        }
        if (! empty($storedAnswer)) {
            if ($storedAnswer == $answer) {
                $this->comment('Answer matches previously stored answer.');
            } else {
                $this->error("Answer differs from previously stored answer: {$storedAnswer}");
            }
            $this->newLine();
        }

        // Print the duration
        $duration = now()->diff(Carbon::createFromTimestamp(LARAVEL_START))->forHumans(['minimumUnit' => 'ms', 'short' => true, 'parts' => 2]);
        $this->comment("Duration: {$duration}");

        return self::SUCCESS;
    }

    public function announcePuzzle(string $year, string $day, string $part, string $puzzleName): void
    {
        $partAsText = ($part == 1) ? 'One' : 'Two';
        $puzzleBanner = "AoC {$year} - Day {$day}: {$puzzleName} - Part {$partAsText}";
        $length = strlen($puzzleBanner) + 12;

        $this->info(str_repeat('*', $length));
        $this->info('*     ' . $puzzleBanner . '     *');
        $this->info(str_repeat('*', $length));
        $this->newLine();
    }
}
