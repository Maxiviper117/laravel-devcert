<?php

namespace Maxiviper117\LaravelDevcert\Actions;

use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Services\DomainManager;
use Maxiviper117\LaravelDevcert\Services\EnvironmentFileManager;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;
use Maxiviper117\LaravelDevcert\Services\MkcertService;
use Maxiviper117\LaravelDevcert\Services\ViteConfigManager;

class SetupLocalHttpsAction
{
    public function __construct(
        private DomainManager $domains,
        private CertificateStore $certificates,
        private HostsFileManager $hosts,
        private MkcertService $mkcert,
        private EnvironmentFileManager $environment,
        private ViteConfigManager $vite,
    ) {}

    public function execute(?string $domain = null, bool $force = false, bool $skipVite = false): array
    {
        $resolvedDomain = $this->domains->resolve($domain);
        $messages = [];

        $this->mkcert->installIfNeeded();
        $messages[] = 'mkcert trust store checked';

        $this->hosts->add($resolvedDomain);
        $messages[] = 'Hosts entry ensured for '.$resolvedDomain;

        $paths = $this->certificates->ensureDirectory($resolvedDomain);
        if ($force || ! $this->certificates->exists($resolvedDomain)) {
            $this->mkcert->generate($resolvedDomain, $paths['cert'], $paths['key']);
            $messages[] = 'Certificate generated for '.$resolvedDomain;
        } else {
            $messages[] = 'Certificate reused for '.$resolvedDomain;
        }

        $this->environment->update([
            'LOCAL_HTTPS_DOMAIN' => $resolvedDomain,
            'LOCAL_HTTPS_CERT' => $paths['cert'],
            'LOCAL_HTTPS_KEY' => $paths['key'],
            'APP_URL' => 'https://'.$resolvedDomain,
        ]);
        $messages[] = '.env updated';

        if (! $skipVite && $this->vite->ensureLocalHttpsConfiguration($resolvedDomain, $paths['cert'], $paths['key'])) {
            $messages[] = 'Vite config updated';
        }

        if ($skipVite) {
            $messages[] = 'Vite config update skipped';
        }

        return [
            'domain' => $resolvedDomain,
            'paths' => $paths,
            'messages' => $messages,
        ];
    }
}
