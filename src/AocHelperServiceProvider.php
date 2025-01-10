<?php

namespace NorthernBytes\AocHelper;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use NorthernBytes\AocHelper\Commands\AocHelperCommand;

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
            ->hasCommand(AocHelperCommand::class);
    }
}
