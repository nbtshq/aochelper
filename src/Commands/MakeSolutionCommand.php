<?php

namespace NorthernBytes\AocHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeSolutionCommand extends Command
{
    public $signature = 'aoc:make-solution { day } { year? } { --force }';

    public $description = 'bar';

    public function handle(): int
    {
        $day = $this->argument('day');
        $year = $this->argument('year') ?: date('Y');
        $force = $this->option('force');

        $this->components->info(sprintf('Preparing Advent of Code %s :: Day %s', $year, $day));

        $path = config('aochelper.solution.path');
        $namespace = Str::of($path)
            ->replace('/', '\\')
            ->ucfirst();

        $filenamePart1 = sprintf('%s/Year_%s/Day%sPart1.php', $path, $year, $day);
        $filenamePart2 = sprintf('%s/Year_%s/Day%sPart2.php', $path, $year, $day);

        if (File::exists($filenamePart1) && File::exists($filenamePart2) && ! $force) {
            $this->components->error('Solution files already exists.');

            return self::FAILURE;
        }

        $this->components->info('Preparing Puzzle file...');

        $stub = Str::of(File::get(__DIR__.'/../../stubs/Puzzle.stub'))
            ->replace('{ $namespace }', sprintf('%s\Year_%s', $namespace, $year))
            ->replace('{ $day }', $day)
            ->replace('{ $year }', $year);

        File::ensureDirectoryExists(base_path(sprintf('%s/Year_%s', $path, $year)));

        if (File::missing(base_path($filenamePart1)) || $force) {
            $this->components->info('Creating Part 1...');

            File::put(base_path($filenamePart1), Str::of($stub)->replace('{ $part }', '1'));
        }

        if (File::missing(base_path($filenamePart2)) || $force) {
            $this->components->info('Creating Part 2...');

            File::put(base_path($filenamePart2), Str::of($stub)->replace('{ $part }', '2'));
        }

        $this->components->success('Solution files created.');

        return self::SUCCESS;
    }
}
