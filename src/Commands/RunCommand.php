<?php

namespace NorthernBytes\AocHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use NorthernBytes\AocHelper\Puzzle;
use Symfony\Component\Console\Input\InputInterface;
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

        $namespace = Str::of(config('aochelper.solution.path'))
            ->replace('/', '\\')
            ->ucfirst();

        $className = sprintf('%s\\Year%s\\Day%sPart%s', $namespace, $year, $day, $part);

        if (! class_exists($className)) {
            $this->components->error('Class not found');

            return self::FAILURE;
        }

        /** @var Puzzle $solution */
        $solution = new $className;

        $solution->setOutput($this->output);
        $solution->setInput($this->input);

        $puzzleName = $solution->getPuzzleName();
        $verbosity = OutputInterface::VERBOSITY_NORMAL;
        $length = Str::length(strip_tags($puzzleName)) + 12;

        $this->info(str_repeat('*', $length), $verbosity);
        $this->info('*     '.$puzzleName.'     *', $verbosity);
        $this->info(str_repeat('*', $length), $verbosity);
        $this->info('', $verbosity);

        $error = $solution->solve();

        if ($error) {
            $this->warn('De fou feil me ' . $solution->getPuzzleAnswer());
        } else {
            $this->info($solution->getPuzzleAnswer() . ' va rätt svar');
        }

        return self::SUCCESS;
    }
}
