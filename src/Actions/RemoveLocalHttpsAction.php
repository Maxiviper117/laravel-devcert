<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Actions;

use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Services\EnvironmentFileManager;
use Maxiviper117\LaravelDevcert\Services\HostsFileManager;

class RemoveLocalHttpsAction
{
    public function __construct(
        private HostsFileManager $hosts,
        private CertificateStore $certificates,
        private EnvironmentFileManager $environment,
    ) {}

    public function execute(string $domain): void
    {
        $this->hosts->remove($domain);
        $this->certificates->delete($domain);
        $this->environment->remove(['LOCAL_HTTPS_DOMAIN', 'LOCAL_HTTPS_CERT', 'LOCAL_HTTPS_KEY']);
    }
}
