<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Actions\StatusAction;
use Maxiviper117\LaravelDevcert\Commands\Concerns\BlocksWsl;

class StatusCommand extends Command
{
    use BlocksWsl;

    protected $signature = 'local-https:status';

    protected $description = 'Show local HTTPS status';

    public function handle(StatusAction $statusAction): int
    {
        if (! $this->guardAgainstWsl()) {
            return self::FAILURE;
        }

        $status = $statusAction->execute();
        $guideUrl = 'https://github.com/Maxiviper117/laravel-devcert';

        $this->components->twoColumnDetail('mkcert installed', $status['mkcert_installed'] ? 'yes' : 'no');
        if (! $status['mkcert_installed']) {
            $this->warn(sprintf('mkcert is not installed or not available on PATH. See %s for setup instructions.', $guideUrl));
        }

        $this->components->twoColumnDetail('caddy installed', $status['caddy_installed'] ? 'yes' : 'no');
        $this->components->twoColumnDetail('caddy version', $status['caddy_version'] ?? 'n/a');
        if (! $status['caddy_installed']) {
            $this->warn(sprintf('Caddy is not installed or not available on PATH. See %s for setup instructions.', $guideUrl));
        }

        $this->components->twoColumnDetail('domain', $status['domain']);
        $this->components->twoColumnDetail('hosts entry', $status['hosts_entry'] ? 'yes' : 'no');
        $this->components->twoColumnDetail('cert path', $status['cert_path']);
        $this->components->twoColumnDetail('key path', $status['key_path']);
        $this->components->twoColumnDetail('cert exists', $status['cert_exists'] ? 'yes' : 'no');
        $this->components->twoColumnDetail('key exists', $status['key_exists'] ? 'yes' : 'no');

        return self::SUCCESS;
    }
}
