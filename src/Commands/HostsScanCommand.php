<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Commands\Concerns\BlocksWsl;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

class HostsScanCommand extends Command
{
    use BlocksWsl;

    protected $signature = 'local-https:hosts:scan';

    protected $description = 'List domains found in the hosts file';

    public function handle(HostsFileManager $hosts): int
    {
        if (! $this->guardAgainstWsl()) {
            return self::FAILURE;
        }

        foreach ($hosts->scan() as $domain) {
            $this->line($domain);
        }

        return self::SUCCESS;
    }
}
