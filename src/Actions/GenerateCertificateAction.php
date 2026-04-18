<?php

namespace Maxiviper117\LaravelDevcert\Actions;

use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Services\MkcertService;

class GenerateCertificateAction
{
    public function __construct(
        private CertificateStore $certificates,
        private MkcertService $mkcert,
    ) {}

    public function execute(string $domain, bool $force = false): array
    {
        $paths = $this->certificates->ensureDirectory($domain);

        if ($force || ! $this->certificates->exists($domain)) {
            $this->mkcert->generate($domain, $paths['cert'], $paths['key']);
        }

        return $paths;
    }
}
