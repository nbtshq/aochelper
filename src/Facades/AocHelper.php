<?php

declare(strict_types=1);

namespace NorthernBytes\AocHelper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \NorthernBytes\AocHelper\AocHelper
 */
class AocHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \NorthernBytes\AocHelper\AocHelper::class;
    }
}
