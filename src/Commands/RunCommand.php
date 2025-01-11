<?php

namespace NorthernBytes\AocHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use NorthernBytes\AocHelper\Puzzle;
use Symfony\Component\Console\Output\OutputInterface;

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
        $puzzleName = $solution->getPuzzleName();
        $partAsText = ($part == 1) ? 'One' : 'Two';
        $puzzleBanner = "AoC {$year} - Day {$day}: {$puzzleName} - Part {$partAsText}";
        $length = strlen($puzzleBanner) + 12;

        $this->info(str_repeat('*', $length));
        $this->info('*     '.$puzzleBanner.'     *');
        $this->info(str_repeat('*', $length));
        $this->newLine();

        // Read puzzle input from file
        /*
        $puzzleInputFile = base_path("/input/{$year}/d{$day}.data");
        $puzzleInput = trim(File::get($puzzleInputFile));
        */
        $puzzleInput = '';
        $solution->setPuzzleInput($puzzleInput);

        // Run the actual solution
        $error = $solution->solve();

        if ($error) {
            $this->warn('De fou feil me '.$solution->getPuzzleAnswer());
            return $error;
        }

        // Print the answer from the solution
        $answerDescription = $solution->getPuzzleAnswerDescription();
        $answer = $solution->getPuzzleAnswer();
        render(<<<HTML
            <div><i>{$answerDescription}:</i> <b><u>{$answer}</u></b></div>
        HTML);

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
}
