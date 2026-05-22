<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withCache(
        cacheDirectory: './.cache/rector',
        cacheClass: FileCacheStorage::class,
        containerCacheDirectory: './.cache/rectorContainer',
    )
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withImportNames()
    ->withFluentCallNewLine()
    ->withParallel(300, 15, 15)
    ->withMemoryLimit('3G')
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
    )
    ->withSkip([
        \Rector\Carbon\Rector\FuncCall\DateFuncCallToCarbonRector::class,
        \Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector::class,
        \Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector::class,
        \Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class,
        \Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector::class,
        \Rector\CodeQuality\Rector\ClassMethod\InlineArrayReturnAssignRector::class,
        \Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector::class,
        \Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector::class,
        \Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector::class,
    ]);
