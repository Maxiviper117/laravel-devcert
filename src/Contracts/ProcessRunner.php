<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Contracts;

use Maxiviper117\LaravelDevcert\Support\ProcessResult;

interface ProcessRunner
{
    public function run(string $command): ProcessResult;
}
