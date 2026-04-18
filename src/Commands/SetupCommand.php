<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Actions\SetupLocalHttpsAction;

class SetupCommand extends Command
{
    protected $signature = 'local-https:setup {domain?} {--force : Regenerate the certificate} {--skip-vite : Do not patch vite.config.js}';

    protected $description = 'Set up trusted local HTTPS for the current project';

    public function handle(SetupLocalHttpsAction $setup): int
    {
        $result = $setup->execute(
            $this->argument('domain'),
            (bool) $this->option('force'),
            (bool) $this->option('skip-vite'),
        );

        $this->components->twoColumnDetail('domain', $result['domain']);
        $this->components->twoColumnDetail('cert', $result['paths']['cert']);
        $this->components->twoColumnDetail('key', $result['paths']['key']);

        foreach ($result['messages'] as $message) {
            $this->info($message);
        }

        return self::SUCCESS;
    }
}
