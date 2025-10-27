<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSets([SetList::DEAD_CODE, SetList::CODE_QUALITY, SetList::EARLY_RETURN, SetList::PHP_82])
    ->withTypeCoverageLevel(0);
