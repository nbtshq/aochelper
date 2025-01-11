<?php

declare(strict_types=1);

namespace NorthernBytes\AocHelper\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Aoc
{
    public static function getClient(): PendingRequest
    {
        return Http::withCookies([
            'session' => config('aochelper.session'),
        ], 'adventofcode.com')
            ->withUserAgent(
                'https://github.com/nbtshq/aochelper by contact@nbts.fi'
            );
    }
}
