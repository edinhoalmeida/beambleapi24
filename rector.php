<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/openai',
        __DIR__ . '/babel',
        __DIR__ . '/payment',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/lang',
        __DIR__ . '/public',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withRules([
        InlineConstructorDefaultToPropertyRector::class,
    ])
    ->withSets([
        // define sets of rules
        // LevelSetList::UP_TO_PHP_80
    ]);
