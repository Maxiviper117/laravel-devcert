<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert;

use Maxiviper117\LaravelDevcert\Actions\GenerateCertificateAction;
use Maxiviper117\LaravelDevcert\Actions\LinkExistingAction;
use Maxiviper117\LaravelDevcert\Actions\RemoveLocalHttpsAction;
use Maxiviper117\LaravelDevcert\Actions\SetupLocalHttpsAction;
use Maxiviper117\LaravelDevcert\Actions\StatusAction;
use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Services\DomainManager;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

class LaravelDevcert
{
    public function __construct(
        protected DomainManager $domains,
        protected CertificateStore $certificates,
        protected HostsFileManager $hosts,
        protected SetupLocalHttpsAction $setupAction,
        protected GenerateCertificateAction $generateAction,
        protected StatusAction $statusAction,
        protected LinkExistingAction $linkExistingAction,
        protected RemoveLocalHttpsAction $removeAction,
    ) {}

    public function setup(?string $domain = null, bool $force = false, bool $skipVite = false): array
    {
        return $this->setupAction->execute($domain, $force, $skipVite);
    }

    public function generateCertificate(string $domain, bool $force = false): array
    {
        return $this->generateAction->execute($domain, $force);
    }

    public function status(): array
    {
        return $this->statusAction->execute();
    }

    public function scanHosts(): array
    {
        return $this->hosts->scan();
    }

    public function addHosts(string $domain): void
    {
        $this->hosts->add($domain);
    }

    public function remove(string $domain): void
    {
        $this->removeAction->execute($domain);
    }

    public function linkExisting(?string $domain = null): array
    {
        return $this->linkExistingAction->execute($domain);
    }
}
