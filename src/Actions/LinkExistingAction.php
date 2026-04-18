<?php

namespace Maxiviper117\LaravelDevcert\Actions;

use Maxiviper117\LaravelDevcert\Services\DomainManager;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

class LinkExistingAction
{
    public function __construct(
        private HostsFileManager $hosts,
        private DomainManager $domains,
        private SetupLocalHttpsAction $setup,
    ) {}

    public function execute(?string $domain = null): array
    {
        $resolved = $domain ?: ($this->hosts->scan()[0] ?? $this->domains->resolve());

        return $this->setup->execute($resolved);
    }
}
