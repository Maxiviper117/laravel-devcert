<?php

namespace Maxiviper117\LaravelDevcert\Commands;

use Illuminate\Console\Command;
use Maxiviper117\LaravelDevcert\Services\CaddyService;
use Maxiviper117\LaravelDevcert\Services\CertificateStore;
use Maxiviper117\LaravelDevcert\Services\DomainManager;
use RuntimeException;

class CaddyCommand extends Command
{
    private const HELP_URL = 'https://github.com/Maxiviper117/laravel-devcert';

    protected $signature = 'local-https:caddy {domain?} {--to= : Upstream URL to proxy to}';

    protected $description = 'Run Caddy as a local HTTPS reverse proxy';

    public function handle(DomainManager $domains, CertificateStore $certificates, CaddyService $caddy): int
    {
        if (! $caddy->installed()) {
            throw new RuntimeException('Caddy is not installed or not available on PATH. See '.self::HELP_URL.' for setup instructions.');
        }

        $domain = $domains->resolve($this->argument('domain'));
        $upstream = trim((string) $this->option('to')) ?: 'http://127.0.0.1:8000';
        $paths = $certificates->paths($domain);
        $caddyfile = rtrim(sys_get_temp_dir(), '\\/').DIRECTORY_SEPARATOR.'laravel-devcert-'.str_replace(['.', '/', '\\'], '-', $domain).'.caddyfile';
        file_put_contents($caddyfile, $caddy->buildCaddyfile($domain, $upstream, $paths['cert'], $paths['key']));
        $process = $caddy->startReverseProxy($caddyfile);
        $stopping = false;

        $cleanup = function () use (&$caddyfile): void {
            if (file_exists($caddyfile)) {
                @unlink($caddyfile);
            }
        };

        $stop = function () use (&$stopping, $process, $cleanup): void {
            $stopping = true;
            if ($process->isRunning()) {
                $process->stop(3, defined('SIGINT') ? SIGINT : null);
            }

            $cleanup();
        };

        register_shutdown_function($stop);

        if (function_exists('pcntl_async_signals') && function_exists('pcntl_signal')) {
            pcntl_async_signals(true);

            $signals = [];

            if (defined('SIGINT')) {
                $signals[] = SIGINT;
            }

            if (defined('SIGTERM')) {
                $signals[] = SIGTERM;
            }

            foreach ($signals as $signal) {
                pcntl_signal($signal, static function (int $signalNumber = 0) use ($stop): void {
                    $stop();
                });
            }
        }

        $this->info(sprintf('Caddy reverse proxy started for https://%s -> %s', $domain, $upstream));
        $this->line('Press Ctrl+C to stop Caddy cleanly.');

        $process->start();

        while ($process->isRunning()) {
            $stdout = $process->getIncrementalOutput();
            $stderr = $process->getIncrementalErrorOutput();

            if ($stdout !== '') {
                $this->output->write($stdout);
            }

            if ($stderr !== '') {
                $this->output->write($stderr);
            }

            usleep(100000);
        }

        $exitCode = $process->wait();
        $stdout = $process->getIncrementalOutput();
        $stderr = $process->getIncrementalErrorOutput();

        if ($stdout !== '') {
            $this->output->write($stdout);
        }

        if ($stderr !== '') {
            $this->output->write($stderr);
        }

        if (! $stopping && $exitCode !== 0) {
            $cleanup();
            throw new RuntimeException(trim($stderr) !== '' ? trim($stderr) : sprintf('Caddy exited with status %s.', $exitCode));
        }

        $cleanup();

        return self::SUCCESS;
    }
}
