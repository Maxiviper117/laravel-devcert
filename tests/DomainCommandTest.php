<?php

it('outputs the resolved domain', function () {
    config()->set('app.name', 'My App');
    config()->offsetUnset('laravel-devcert.local_https_domain');
    config()->set('app.url', 'http://localhost');

    $this->artisan('local-https:domain')
        ->expectsOutput('my-app.test')
        ->assertSuccessful();
});

it('outputs an explicit domain argument', function () {
    $this->artisan('local-https:domain custom.test')
        ->expectsOutput('custom.test')
        ->assertSuccessful();
});

it('outputs the configured domain when available', function () {
    config()->set('laravel-devcert.local_https_domain', 'configured.test');

    $this->artisan('local-https:domain')
        ->expectsOutput('configured.test')
        ->assertSuccessful();
});
