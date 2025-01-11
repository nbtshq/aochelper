<?php

declare(strict_types=1);

namespace NorthernBytes\AocHelper\Support;

use Illuminate\Support\Facades\File;
use NorthernBytes\AocHelper\Interfaces\PuzzleInputProviderInterface;

class AocdWrapper implements PuzzleInputProviderInterface
{
    /**
     * Status of the wrapper. Wrapper will return only empty strings if this is set to false.
     */
    private bool $enabled = false;

    /**
     * The path to the aocd executable
     */
    private string $aocdPath;

    /**
     * The version number of the aocd executable used
     */
    private string $aocdVersion;

    /**
     * The path to the aocd data directory
     */
    private string $dataDirectory;

    public function __construct()
    {
        if (! config('aochelper.aocdwrapper.enable')) {
            // Nothing to do here, the wrapper is not enabled
            return;
        }

        $this->discoverAocdExecutable();
        $this->discoverDataDirectory();

        $this->enabled = true;
    }

    /**
     * Search for aocd executable and setup necessary class variables
     */
    private function discoverAocdExecutable(): void
    {
        if (! empty(config('aochelper.aocdwrapper.aocd_path'))) {
            // aocd path manually defined in env
            $aocd_path = config('aochelper.aocdwrapper.aocd_path');
        } else {
            // Searching for aocd executable in PATH
            exec('which aocd 2>/dev/null', $output, $return_var);
            if ($return_var !== 0) {
                throw new \Exception('aocd executable not found in PATH and not defined in AOCD_PATH');
            }
            $aocd_path = array_pop($output);
        }
        exec("{$aocd_path} --version 2>/dev/null", $output, $return_var);
        if ($return_var !== 0) {
            throw new \Exception("Could not get version of aocd executable at {$aocd_path}");
        }

        $aocd_version = array_pop($output);

        $this->aocdPath = $aocd_path;
        $this->aocdVersion = $aocd_version;

    }

    /**
     * Search for aocd data directory and setup necessary class variables
     */
    private function discoverDataDirectory(): void
    {
        if (! empty(config('aochelper.aocdwrapper.aocd_data_dir'))) {
            // aocd data directory manually defined in env
            $aocd_data_dir = config('aochelper.aocdwrapper.aocd_data_dir');
        } else {
            // aocd data directory not defined, searching under assumed default path
            $aocd_dir = '~/.config/aocd';
            $real_aocd_dir = exec("cd {$aocd_dir} 2>/dev/null && pwd", $output, $return_var);
            if ($return_var !== 0) {
                throw new \Exception("Expected aocd data directory not found at {$aocd_dir}");
            }

            $token2id_path = $real_aocd_dir.'/token2id.json';
            $tokens = json_decode(File::get($token2id_path), true);
            $aoc_user_id = array_pop($tokens);

            $aocd_data_dir = $real_aocd_dir.'/'.$aoc_user_id;
        }

        if (! File::isDirectory($aocd_data_dir)) {
            throw new \Exception("Expected data directory for AoC user not found at {$aocd_data_dir}");
        }

        $this->dataDirectory = $aocd_data_dir;

    }

    public function getPuzzleInput(int $year, int $day): string
    {
        if (! $this->enabled) {
            return '';
        }

        exec("{$this->aocdPath} {$day} {$year} 2>/dev/null", $output, $return_var);

        if ($return_var !== 0) {
            throw new \Exception("aocd exited with non-zero status {$return_var}");
        }

        return implode("\n", $output);
    }
}
