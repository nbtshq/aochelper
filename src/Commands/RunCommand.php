<?php

namespace NorthernBytes\AocHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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

        $className = sprintf('%s\\Year_%s\\Day%sPart%s', $namespace, $year, $day, $part);

        if ((new $className) == null) {
            $this->components->error('Class not found');
            return self::FAILURE;
        }

        $this->runCommand($className, [], $this->output);

        return self::SUCCESS;
    }
}
