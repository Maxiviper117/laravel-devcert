<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Actions\GenerateCertificateAction;

class CertGenerateCommand extends Command
{
    protected $signature = 'local-https:cert:generate {domain} {--force : Regenerate even if files exist}';

    protected $description = 'Generate or reuse a local HTTPS certificate';

    public function handle(GenerateCertificateAction $generate): int
    {
        $paths = $generate->execute($this->argument('domain'), (bool) $this->option('force'));

        $this->components->twoColumnDetail('cert', $paths['cert']);
        $this->components->twoColumnDetail('key', $paths['key']);

        return self::SUCCESS;
    }
}
