<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpVersion(80200)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
    )
    ->withSkip([
        __DIR__.'/workbench',
    ])
    ->withImportNames(
        importNames: true,
        importDocBlockNames: true,
        removeUnusedImports: true,
    )
    ->withParallel(
        timeoutSeconds: 120,
        maxNumberOfProcess: getenv('CI') ? 1 : 4,
        jobSize: 20,
    )
    ->withCache(
        cacheDirectory: __DIR__.'/build/rector',
    );
