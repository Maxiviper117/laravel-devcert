<?php

declare(strict_types=1);

namespace Maxiviper117\LaravelDevcert\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Maxiviper117\LaravelDevcert\LaravelDevcert
 */
class LaravelDevcert extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Maxiviper117\LaravelDevcert\LaravelDevcert::class;
    }
}
