<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Actions\RemoveLocalHttpsAction;
use Maxiviper117\LaravelDevcert\Commands\Concerns\BlocksWsl;
use Maxiviper117\LaravelDevcert\Exceptions\HostsFilePermissionException;

class RemoveCommand extends Command
{
    use BlocksWsl;

    protected $signature = 'local-https:remove {domain}';

    protected $description = 'Remove a domain, its certificate, and env settings';

    public function handle(RemoveLocalHttpsAction $remove): int
    {
        if (! $this->guardAgainstWsl()) {
            return self::FAILURE;
        }

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
