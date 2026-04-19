<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Actions\GenerateCertificateAction;
use Maxiviper117\LaravelDevcert\Commands\Concerns\BlocksWsl;

class CertGenerateCommand extends Command
{
    use BlocksWsl;

    protected $signature = 'local-https:cert:generate {domain} {--force : Regenerate even if files exist}';

    protected $description = 'Generate or reuse a local HTTPS certificate';

    public function handle(GenerateCertificateAction $generate): int
    {
        if (! $this->guardAgainstWsl()) {
            return self::FAILURE;
        }

        $paths = $generate->execute($this->argument('domain'), (bool) $this->option('force'));

        $this->components->twoColumnDetail('cert', $paths['cert']);
        $this->components->twoColumnDetail('key', $paths['key']);

        return self::SUCCESS;
    }
}
