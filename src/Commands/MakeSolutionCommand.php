<?php

namespace NorthernBytes\AocHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use NorthernBytes\AocHelper\Support\Aoc;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSolutionCommand extends Command
{
    public $signature = 'aoc:make-solution { day } { year? } { --force }';

    public $description = 'bar';

    public function handle(): int
    {
        $day = $this->argument('day');
        $year = $this->argument('year') ?: date('Y');
        $force = $this->option('force');

        $this->components->info(
            sprintf('Preparing Advent of Code %s :: Day %s', $year, $day),
            OutputInterface::VERBOSITY_VERBOSE
        );

        $path = config('aochelper.solution.path');
        $namespace = Str::of($path)
            ->replace('/', '\\')
            ->ucfirst();

        $filenamePart1 = sprintf('%s/Year%s/Day%sPart1.php', $path, $year, $day);
        $filenamePart2 = sprintf('%s/Year%s/Day%sPart2.php', $path, $year, $day);

        if (File::exists($filenamePart1) && File::exists($filenamePart2) && ! $force) {
            $this->components->error('Solution files already exists.');

            return self::FAILURE;
        }

        $this->components->info('Fetching Puzzle name...', OutputInterface::VERBOSITY_VERBOSE);

        $puzzle = Aoc::getClient()
            ->get(sprintf('https://adventofcode.com/%d/day/%d', $year, $day))
            ->getBody()->getContents();

        $name = substr($puzzle, stripos($puzzle, '<article class="day-desc">') + 30, -1);
        $name = substr($name, 0, stripos($name, '</h2>'));

        $name = preg_replace('/^--- Day \d+: /', '', $name);
        $name = preg_replace('/ ---$/', '', $name);

        $stub = Str::of(File::get(__DIR__.'/../../stubs/Puzzle.stub'))
            ->replace('{ $namespace }', sprintf('%s\Year%s', $namespace, $year))
            ->replace('{ $day }', $day)
            ->replace('{ $year }', $year)
            ->replace('{ $name }', $name);

        File::ensureDirectoryExists(base_path(sprintf('%s/Year%s', $path, $year)));

        $this->saveSolution($filenamePart1, '1', $stub, $force);
        $this->saveSolution($filenamePart2, '2', $stub, $force);

        $this->components->success('Solution files created.');

        return self::SUCCESS;
    }

    public function saveSolution(string $filename, string $part, Stringable $stub, bool $force): void
    {
        if (File::missing(base_path($filename)) || $force) {
            $this->components->info("Creating Part $part...", OutputInterface::VERBOSITY_VERBOSE);

            File::put(
                base_path($filename),
                Str::of($stub)
                    ->replace('{ $part }', $part)
            );
        }
    }
}
