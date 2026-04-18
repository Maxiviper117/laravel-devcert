<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Contracts;

interface ManagedProcessFactory
{
    /**
     * @param  array<int, string>  $command
     */
    public function create(array $command): ManagedProcess;
}
