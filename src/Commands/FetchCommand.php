<?php

namespace NorthernBytes\AocHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use NorthernBytes\AocHelper\Support\Aoc;

class FetchCommand extends Command
{
    public $signature = 'aoc:fetch { day } { year? }';

    public $description = 'Fetch AoC input data for specified day';

    public function handle(): int
    {
        $day = $this->argument('day');
        $year = $this->argument('year') ?: date('Y');

        $inputPath = storage_path('aoc/input');
        $inputFile = $inputPath.sprintf('/%d_%02d_input.txt', $year, $day);

        if (File::exists($inputFile)) {
            $this->components->warn(sprintf('Input file %s already exists.', $inputFile));

            return self::SUCCESS;
        }

        File::ensureDirectoryExists($inputPath);

        $inputRequest = Aoc::getClient()
            ->get(sprintf('https://adventofcode.com/%d/day/%d/input', $year, $day));

        if ($inputRequest->failed()) {
            $this->components->error('Could not read input from adventofcode.com');

            return self::FAILURE;
        }

        File::put($inputFile, rtrim($inputRequest->getBody()->getContents()));

        $this->components->success(sprintf('Successfully saved input to %s', $inputFile));

        return self::SUCCESS;
    }
}
