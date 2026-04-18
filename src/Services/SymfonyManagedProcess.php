<?php

namespace Maxiviper117\LaravelDevcert\Services;

use Maxiviper117\LaravelDevcert\Contracts\ManagedProcess;
use Symfony\Component\Process\Process;

class SymfonyManagedProcess implements ManagedProcess
{
    public function __construct(private Process $process)
    {
        $this->process->setTimeout(null);
    }

    public function start(): void
    {
        $this->process->start();
    }

    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }

    public function getIncrementalOutput(): string
    {
        return $this->process->getIncrementalOutput();
    }

    public function getIncrementalErrorOutput(): string
    {
        return $this->process->getIncrementalErrorOutput();
    }

    public function stop(int $timeout = 3, ?int $signal = null): int
    {
        return $this->process->stop($timeout, $signal);
    }

    public function wait(): int
    {
        return $this->process->wait();
    }

    public function getExitCode(): ?int
    {
        return $this->process->getExitCode();
    }
}
