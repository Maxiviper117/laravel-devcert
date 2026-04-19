<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Actions\SetupLocalHttpsAction;
use Maxiviper117\LaravelDevcert\Commands\Concerns\BlocksWsl;
use Maxiviper117\LaravelDevcert\Exceptions\HostsFilePermissionException;

class SetupCommand extends Command
{
    use BlocksWsl;

    protected $signature = 'local-https:setup {domain?} {--force : Regenerate the certificate} {--skip-vite : Do not patch vite.config.js}';

    protected $description = 'Set up trusted local HTTPS for the current project';

    public function handle(SetupLocalHttpsAction $setup): int
    {
        if (! $this->guardAgainstWsl()) {
            return self::FAILURE;
        }

        try {
            $result = $setup->execute(
                $this->argument('domain'),
                (bool) $this->option('force'),
                (bool) $this->option('skip-vite'),
            );
        } catch (HostsFilePermissionException $hostsFilePermissionException) {
            $this->error($hostsFilePermissionException->getMessage());

            return self::FAILURE;
        }

        $this->components->twoColumnDetail('domain', $result['domain']);
        $this->components->twoColumnDetail('cert', $result['paths']['cert']);
        $this->components->twoColumnDetail('key', $result['paths']['key']);

        foreach ($result['messages'] as $message) {
            $this->info($message);
        }

        return self::SUCCESS;
    }
}
