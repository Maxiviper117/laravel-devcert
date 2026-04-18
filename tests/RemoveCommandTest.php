<?php

use Maxiviper117\LaravelDevcert\Actions\RemoveLocalHttpsAction;

it('removes local https configuration for a domain', function () {
    $remove = Mockery::mock(RemoveLocalHttpsAction::class);
    $remove->shouldReceive('execute')
        ->with('example.test')
        ->once();

    app()->instance(RemoveLocalHttpsAction::class, $remove);

    $this->artisan('local-https:remove example.test')
        ->expectsOutputToContain('Local HTTPS configuration removed')
        ->assertSuccessful();
});

it('removes configuration for different domains', function () {
    $remove = Mockery::mock(RemoveLocalHttpsAction::class);
    $remove->shouldReceive('execute')
        ->with('myapp.local')
        ->once();

    app()->instance(RemoveLocalHttpsAction::class, $remove);

    $this->artisan('local-https:remove myapp.local')
        ->expectsOutputToContain('Local HTTPS configuration removed')
        ->assertSuccessful();
});
