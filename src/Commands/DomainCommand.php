<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Commands\Concerns\BlocksWsl;
use Maxiviper117\LaravelDevcert\Services\DomainManager;

class DomainCommand extends Command
{
    use BlocksWsl;

    protected $signature = 'local-https:domain {domain?}';

    protected $description = 'Resolve the local HTTPS domain for this project';

    public function handle(DomainManager $domains): int
    {
        if (! $this->guardAgainstWsl()) {
            return self::FAILURE;
        }

        $this->line($domains->resolve($this->argument('domain')));

        return self::SUCCESS;
    }
}
