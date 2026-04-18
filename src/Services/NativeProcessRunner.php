<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Services;

use Maxiviper117\LaravelDevcert\Contracts\ProcessRunner;
use Maxiviper117\LaravelDevcert\Support\ProcessResult;

class NativeProcessRunner implements ProcessRunner
{
    public function run(string $command): ProcessResult
    {
        $output = [];
        $exitCode = 0;

        exec($command.' 2>&1', $output, $exitCode);

        return new ProcessResult($exitCode, $output);
    }
}
