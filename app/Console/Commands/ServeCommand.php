<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;
use Illuminate\Support\Carbon;
use Throwable;

class ServeCommand extends BaseServeCommand
{
    protected function getDateFromLine($line)
    {
        $regex = env('PHP_CLI_SERVER_WORKERS', 1) > 1
            ? '/^\[\d+]\s\[([a-zA-Z0-9: ]+)\]/'
            : '/^\[([^\]]+)\]/';

        $line = preg_replace('/\x1B\[[0-9;]*m/', '', $line);
        $line = str_replace('  ', ' ', $line);

        if (!preg_match($regex, $line, $matches) || !isset($matches[1])) {
            return Carbon::now();
        }

        try {
            return Carbon::createFromFormat('D M d H:i:s Y', $matches[1]);
        } catch (Throwable $e) {
            return Carbon::now();
        }
    }
}
