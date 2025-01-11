<?php

namespace NorthernBytes\AocHelper;

use NorthernBytes\AocHelper\Commands\AocdConfigTestCommand;
use NorthernBytes\AocHelper\Commands\FetchCommand;
use NorthernBytes\AocHelper\Commands\MakeSolutionCommand;
use NorthernBytes\AocHelper\Commands\RunCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AocHelperServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('aochelper')
            ->hasConfigFile()
            ->hasCommand(MakeSolutionCommand::class)
            ->hasCommand(FetchCommand::class)
            ->hasCommand(RunCommand::class)
            ->hasCommand(AocdConfigTestCommand::class);
    }
}
