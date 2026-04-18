<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert;

use Maxiviper117\LaravelDevcert\Commands\CaddyCommand;
use Maxiviper117\LaravelDevcert\Commands\CertGenerateCommand;
use Maxiviper117\LaravelDevcert\Commands\DomainCommand;
use Maxiviper117\LaravelDevcert\Commands\HostsAddCommand;
use Maxiviper117\LaravelDevcert\Commands\HostsScanCommand;
use Maxiviper117\LaravelDevcert\Commands\LinkExistingCommand;
use Maxiviper117\LaravelDevcert\Commands\RemoveCommand;
use Maxiviper117\LaravelDevcert\Commands\SetupCommand;
use Maxiviper117\LaravelDevcert\Commands\StatusCommand;
use Maxiviper117\LaravelDevcert\Contracts\ManagedProcessFactory;
use Maxiviper117\LaravelDevcert\Contracts\ProcessRunner;
use Maxiviper117\LaravelDevcert\Services\NativeProcessRunner;
use Maxiviper117\LaravelDevcert\Services\SymfonyManagedProcessFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelDevcertServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-devcert')
            ->hasConfigFile('laravel-devcert')
            ->hasCommand(SetupCommand::class)
            ->hasCommand(DomainCommand::class)
            ->hasCommand(HostsAddCommand::class)
            ->hasCommand(HostsScanCommand::class)
            ->hasCommand(CertGenerateCommand::class)
            ->hasCommand(CaddyCommand::class)
            ->hasCommand(LinkExistingCommand::class)
            ->hasCommand(StatusCommand::class)
            ->hasCommand(RemoveCommand::class);

        $this->app->bind(ProcessRunner::class, NativeProcessRunner::class);
        $this->app->bind(ManagedProcessFactory::class, SymfonyManagedProcessFactory::class);
    }
}
