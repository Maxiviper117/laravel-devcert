<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Commands\Concerns\BlocksWsl;
use Maxiviper117\LaravelDevcert\Exceptions\HostsFilePermissionException;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

class HostsAddCommand extends Command
{
    use BlocksWsl;

    protected $signature = 'local-https:hosts:add {domain}';

    protected $description = 'Add a domain to the hosts file';

    public function handle(HostsFileManager $hosts): int
    {
        if (! $this->guardAgainstWsl()) {
            return self::FAILURE;
        }

        try {
            $hosts->add($this->argument('domain'));
        } catch (HostsFilePermissionException $hostsFilePermissionException) {
            $this->error($hostsFilePermissionException->getMessage());

            return self::FAILURE;
        }

        $this->info('Hosts entry added.');

        return self::SUCCESS;
    }
}
