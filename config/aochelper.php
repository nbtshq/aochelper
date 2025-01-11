<?php

// config for NorthernBytes/AocHelper
return [
    'session' => env('AOC_HELPER_SESSION'),
    'solution' => [
        'path' => env('AOC_HELPER_PATH', 'app/Solutions'),
    ],
    'aocdwrapper' => [
        'enable' => env('AOCD_ENABLE', false),
        'aocd_path' => env('AOCD_PATH'),
        'aocd_token_path' => env('AOCD_TOKEN_PATH'),
        'aocd_data_dir' => env('AOCD_DATA_DIR'),
    ],
];
