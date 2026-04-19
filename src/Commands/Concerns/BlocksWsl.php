<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Commands\Concerns;

use Maxiviper117\LaravelDevcert\Support\OperatingSystem;

trait BlocksWsl
{
    protected function guardAgainstWsl(): bool
    {
        if (! OperatingSystem::isWsl()) {
            return true;
        }

        $this->error('Sorry, WSL is not supported. Run this command from Windows instead.');

        return false;
    }
}
