<?php

namespace NorthernBytes\AocHelper\Commands;

use Illuminate\Console\Command;

class AocHelperCommand extends Command
{
    public $signature = 'aochelper';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
