<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Actions\LinkExistingAction;
use Maxiviper117\LaravelDevcert\Exceptions\HostsFilePermissionException;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

class LinkExistingCommand extends Command
{
    protected $signature = 'local-https:link-existing {domain?}';

    protected $description = 'Link the project to an existing domain';

    public function handle(LinkExistingAction $linkExisting, HostsFileManager $hosts): int
    {
        $domains = $hosts->scan();
        $domain = $this->argument('domain') ?: ($domains !== [] ? $this->choice('Select a domain', $domains) : null);

        if (! $domain) {
            $this->warn('No existing domain found.');

            return self::FAILURE;
        }

        try {
            $result = $linkExisting->execute($domain);
        } catch (HostsFilePermissionException $hostsFilePermissionException) {
            $this->error($hostsFilePermissionException->getMessage());

            return self::FAILURE;
        }

        foreach ($result['messages'] as $message) {
            $this->info($message);
        }

        return self::SUCCESS;
    }
}
