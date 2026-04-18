<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Actions\RemoveLocalHttpsAction;
use Maxiviper117\LaravelDevcert\Exceptions\HostsFilePermissionException;

class RemoveCommand extends Command
{
    protected $signature = 'local-https:remove {domain}';

    protected $description = 'Remove a domain, its certificate, and env settings';

    public function handle(RemoveLocalHttpsAction $remove): int
    {
        try {
            $remove->execute($this->argument('domain'));
        } catch (HostsFilePermissionException $hostsFilePermissionException) {
            $this->error($hostsFilePermissionException->getMessage());

            return self::FAILURE;
        }

        $this->info('Local HTTPS configuration removed.');

        return self::SUCCESS;
    }
}
