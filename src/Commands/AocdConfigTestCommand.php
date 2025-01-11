<?php

namespace NorthernBytes\AocHelper\Commands;

use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Facades\File;

class AocdConfigTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:aocd-config-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the configuration for the aocd wrapper';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // First check configuration
        if (config('aochelper.aocdwrapper.enable')) {
            $this->info('aocd wrapper is enabled');
        } else {
            $this->warn('aocd wrapper is disabled');
            $this->line('Recommendation: set AOCD_ENABLE=true in .env to enable');
        }
        $this->newLine();

        // Try to find aocd executable
        if (!empty(config('aochelper.aocdwrapper.aocd_path'))) {
            $aocd_path = config('aochelper.aocdwrapper.aocd_path');
            $this->info("aocd path manually defined in env: AOCD_PATH={$aocd_path}");
        } else {
            exec('which aocd 2>/dev/null', $output, $return_var);
            if ($return_var !== 0) {
                $this->error('aocd executable not found in PATH');
                $this->line('Recommendation: modify PATH or manually define AOCD_PATH=/path/to/aocd in .env');
                return self::FAILURE;
            }
            $aocd_path = array_pop($output);
            $this->info("aocd executable found in PATH: {$aocd_path}");
        }
        exec("{$aocd_path} --version 2>/dev/null", $output, $return_var);
        if ($return_var !== 0) {
            var_dump($return_var);
            $this->error("Could not get version of aocd executable at {$aocd_path}");
            return self::FAILURE;
        }
        $aocd_version = array_pop($output);
        $this->info("{$aocd_version} found at {$aocd_path}");
        $this->newLine();

        // Try to find aocd-token executable
        if (!empty(config('aochelper.aocdwrapper.aocd_token_path'))) {
            $aocd_token_path = config('aochelper.aocdwrapper.aocd_token_path');
            $this->info("aocd-token path manually defined in env: AOCD_TOKEN_PATH={$aocd_token_path}");
        } else {
            exec('which aocd-token 2>/dev/null', $output, $return_var);
            if ($return_var !== 0) {
                $this->error('aocd-token executable not found in PATH');
                $this->line('Recommendation: modify PATH or manually define AOCD_TOKEN_PATH=/path/to/aocd-token in .env');
                return self::FAILURE;
            }
            $aocd_token_path = array_pop($output);
            $this->info("aocd-token executable found in PATH: {$aocd_token_path}");
        }
        exec("{$aocd_token_path} --check 2>/dev/null", $output, $return_var);
        if ($return_var !== 0) {
            $this->error("Could not get any AoC session tokens from aocd-token at {$aocd_token_path}");
            return self::FAILURE;
        }
        $aocd_token_line = array_pop($output);
        if (!preg_match('/(.*) \((.*)\) is (dead|alive)/', $aocd_token_line, $matches)) {
            $this->error("Could not parse output from aocd-token at {$aocd_token_path}");
            return self::FAILURE;
        }
        $aocd_token = $matches[1];
        $aocd_token_path = $matches[2];
        $aocd_token_status = $matches[3];
        if ($aocd_token_status == 'alive') {
            $this->info("AoC session token @ {$aocd_token_path} is alive");
        } else {
            $this->warn("AoC session token @ {$aocd_token_path} is dead");
        }
        $this->newLine();

        // Try to find aocd data directory
        if (!empty(config('aochelper.aocdwrapper.aocd_data_dir'))) {
            $aocd_data_dir = config('aochelper.aocdwrapper.aocd_data_dir');
            $this->info("aocd data directory manually defined in env: AOCD_DATA_DIR={$aocd_data_dir}");
            if (!File::isDirectory($aocd_data_dir)) {
                $this->error("Expected data directory for AoC user not found at {$aocd_data_dir}");
                return self::FAILURE;
            }
        } else {
            $aocd_dir = '~/.config/aocd';
            $this->info("aocd data directory not defined, searching under default {$aocd_dir}");
            $real_aocd_dir = exec("cd {$aocd_dir} 2>/dev/null && pwd", $output, $return_var);
            if ($return_var !== 0) {
                $this->error("Expected aocd data directory not found at {$aocd_dir}");
                return self::FAILURE;
            }
            $token2id_path = $real_aocd_dir . '/token2id.json';
            $tokens = json_decode(File::get($token2id_path), true);
            $aoc_user_id = array_pop($tokens);
            $aocd_data_dir = $real_aocd_dir . '/' . $aoc_user_id;
            if (!File::isDirectory($aocd_data_dir)) {
                $this->error("Expected data directory for AoC user not found at {$aocd_data_dir}");
                return self::FAILURE;
            }
            $this->info("Discovered data directory for AoC user {$aoc_user_id} at {$aocd_data_dir}");
            $this->line("Recommendation: set AOCD_DATA_DIR={$aocd_data_dir} in .env");
        }
        $this->newLine();

        $this->info("Everything looks good, aocd wrapper should be good to go!");
        return self::SUCCESS;
    }
}
