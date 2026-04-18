<?php

namespace Maxiviper117\LaravelDevcert\Actions;

use Maxiviper117\LaravelDevcert\Services\CaddyService;
use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Services\DomainManager;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;
use Maxiviper117\LaravelDevcert\Services\MkcertService;

class StatusAction
{
    public function __construct(
        private DomainManager $domains,
        private CertificateStore $certificates,
        private HostsFileManager $hosts,
        private MkcertService $mkcert,
        private CaddyService $caddy,
    ) {}

    public function execute(): array
    {
        $domain = $this->domains->resolve();
        $paths = $this->certificates->paths($domain);

        return [
            'mkcert_installed' => $this->mkcert->installed(),
            'caddy_installed' => $this->caddy->installed(),
            'caddy_version' => $this->caddy->version(),
            'domain' => $domain,
            'hosts_entry' => $this->hosts->contains($domain),
            'cert_path' => $paths['cert'],
            'key_path' => $paths['key'],
            'cert_exists' => file_exists($paths['cert']),
            'key_exists' => file_exists($paths['key']),
        ];
    }
}
