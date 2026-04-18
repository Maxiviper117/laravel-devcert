<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Services\DomainManager;

class DomainCommand extends Command
{
    protected $signature = 'local-https:domain {domain?}';

    protected $description = 'Resolve the local HTTPS domain for this project';

    public function handle(DomainManager $domains): int
    {
        $this->line($domains->resolve($this->argument('domain')));

        return self::SUCCESS;
    }
}
