<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Services;

use Maxiviper117\LaravelDevcert\Contracts\ManagedProcess;
use Maxiviper117\LaravelDevcert\Contracts\ManagedProcessFactory;
use Symfony\Component\Process\Process;

class SymfonyManagedProcessFactory implements ManagedProcessFactory
{
    public function create(array $command): ManagedProcess
    {
        return new SymfonyManagedProcess(new Process($command));
    }
}
