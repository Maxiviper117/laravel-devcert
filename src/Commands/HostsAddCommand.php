<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

class HostsAddCommand extends Command
{
    protected $signature = 'local-https:hosts:add {domain}';

    protected $description = 'Add a domain to the hosts file';

    public function handle(HostsFileManager $hosts): int
    {
        $hosts->add($this->argument('domain'));
        $this->info('Hosts entry added.');

        return self::SUCCESS;
    }
}
