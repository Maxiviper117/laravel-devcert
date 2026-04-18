<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

class HostsScanCommand extends Command
{
    protected $signature = 'local-https:hosts:scan';

    protected $description = 'List domains found in the hosts file';

    public function handle(HostsFileManager $hosts): int
    {
        foreach ($hosts->scan() as $domain) {
            $this->line($domain);
        }

        return self::SUCCESS;
    }
}
