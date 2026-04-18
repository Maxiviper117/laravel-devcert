<?php

use Maxiviper117\LaravelDevcert\Services\DomainManager;

it('builds the default local domain from app name', function () {
    config()->set('app.name', 'My App');
    config()->offsetUnset('laravel-devcert.local_https_domain');
    config()->set('app.url', 'http://localhost');

    expect(app(DomainManager::class)->resolve())->toBe('my-app.test');
});

it('prefers the configured local https domain', function () {
    config()->set('app.name', 'My App');
    config()->set('laravel-devcert.local_https_domain', 'example.test');
    config()->set('app.url', 'https://example.test');

    expect(app(DomainManager::class)->resolve())->toBe('example.test');
});

it('keeps an explicit domain intact', function () {
    expect(app(DomainManager::class)->resolve('custom.test'))->toBe('custom.test');
});
