<?php

namespace NorthernBytes\AocHelper\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use NorthernBytes\AocHelper\Puzzle;

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
        $puzzleInputFile = sprintf(
            '%s/%d_%02d_input.txt',
            storage_path('aoc/input'),
            $year,
            $day
        );

        if (File::exists($puzzleInputFile)) {
            $solution->setPuzzleInput(trim(File::get($puzzleInputFile)));
        }

        // Run the actual solution
        $solution->solve();

        // Print the answer from the solution
        $answerDescription = $solution->getPuzzleAnswerDescription();
        $answer = $solution->getPuzzleAnswer();

        render(<<<HTML
            <div><i>{$answerDescription}:</i> <b><u>{$answer}</u></b></div>
        HTML);

        $this->newLine();

        // Check the result against answer file, if one exists
        /*
        $answerFile = base_path("/answers/{$year}/d{$day}p{$part}.data");
        if (File::exists($answerFile)) {
            $storedAnswer = trim(File::get($answerFile));
            if ($storedAnswer == $answer) {
                $this->comment("Answer matches stored answer file.");
            } else {
                $this->error("Answer differs from stored answer file: {$storedAnswer}");
            }
        }
        $this->newLine();
        */

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
        $this->info('*     '.$puzzleBanner.'     *');
        $this->info(str_repeat('*', $length));
        $this->newLine();
    }
}
