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

it('rejects domains with spaces', function () {
    app(DomainManager::class)->resolve('my app.test');
})->throws(InvalidArgumentException::class, 'cannot contain spaces');

it('rejects domains with consecutive dots', function () {
    app(DomainManager::class)->resolve('test..example.test');
})->throws(InvalidArgumentException::class, 'consecutive dots');

it('rejects domains with invalid characters', function () {
    app(DomainManager::class)->resolve('test@example.test');
})->throws(InvalidArgumentException::class, 'Invalid domain name format');

it('rejects domains starting with a dot', function () {
    app(DomainManager::class)->resolve('.example.test');
})->throws(InvalidArgumentException::class, 'empty labels');

it('rejects labels starting with hyphens', function () {
    app(DomainManager::class)->resolve('-example.test');
})->throws(InvalidArgumentException::class, "'-example'");

it('rejects labels ending with hyphens', function () {
    app(DomainManager::class)->resolve('example-.test');
})->throws(InvalidArgumentException::class, "'example-'");

it('accepts valid complex domains', function () {
    expect(app(DomainManager::class)->resolve('sub.example.test'))->toBe('sub.example.test');
    expect(app(DomainManager::class)->resolve('deep.sub.example.test'))->toBe('deep.sub.example.test');
    expect(app(DomainManager::class)->resolve('my-app.example.test'))->toBe('my-app.example.test');
});
