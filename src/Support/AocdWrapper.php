<?php
declare(strict_types=1);

namespace NorthernBytes\AocHelper\Support;
use Illuminate\Support\Facades\File;

class AocdWrapper
{
    /**
     * Status of the wrapper. Wrapper will return only empty strings if this is set to false.
     */
    private bool $enabled = false;

    /**
     * The path to the aocd data directory
     */
    private string $dataDirectory;

    function __construct()
    {
        if (!config('aochelper.aocdwrapper.enable')) {
            // Nothing to do here, the wrapper is not enabled
            return;
        }

        $this->dataDirectory = $this->discoverDataDirectory();

        $this->enabled = true;
    }

    /**
     * Search for and return the path to the aocd data directory
     */
    private function discoverDataDirectory(): string
    {
        if (!empty(config('aochelper.aocdwrapper.aocd_data_dir'))) {
            // aocd data directory manually defined in env
            $aocd_data_dir = config('aochelper.aocdwrapper.aocd_data_dir');
        } else {
            // aocd data directory not defined, searching under assumed default path
            $aocd_dir = '~/.config/aocd';
            $real_aocd_dir = exec("cd {$aocd_dir} 2>/dev/null && pwd", $output, $return_var);
            if ($return_var !== 0) {
                throw new \Exception("Expected aocd data directory not found at {$aocd_dir}");
            }

            $token2id_path = $real_aocd_dir . '/token2id.json';
            $tokens = json_decode(File::get($token2id_path), true);
            $aoc_user_id = array_pop($tokens);

            $aocd_data_dir = $real_aocd_dir . '/' . $aoc_user_id;
        }

        if (!File::isDirectory($aocd_data_dir)) {
            throw new \Exception("Expected data directory for AoC user not found at {$aocd_data_dir}");
        }

        return $aocd_data_dir;
    }
}
