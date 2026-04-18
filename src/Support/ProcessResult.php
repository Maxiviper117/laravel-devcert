<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Support;

class ProcessResult
{
    public function __construct(
        public int $exitCode,
        public array $output = [],
    ) {}
}
