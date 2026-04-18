<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Contracts;

interface ManagedProcess
{
    public function start(): void;

    public function isRunning(): bool;

    public function getIncrementalOutput(): string;

    public function getIncrementalErrorOutput(): string;

    public function stop(int $timeout = 3, ?int $signal = null): int;

    public function wait(): int;

    public function getExitCode(): ?int;
}
