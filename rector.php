<?php

use Rector\Arguments\Rector\FuncCall\FunctionArgumentDefaultValueReplacerRector;
use Rector\Config\RectorConfig;
use Rector\Php73\Rector\BooleanOr\IsCountableRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php80\Rector\FuncCall\ClassOnObjectRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withSkip([
        ClassOnObjectRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
        FunctionArgumentDefaultValueReplacerRector::class,
        IsCountableRector::class,
        MixedTypeRector::class,
        StringableForToStringRector::class,
        NullToStrictStringFuncCallArgRector::class,
    ])
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/Modules',
    ])
    ->withSets([
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelLevelSetList::UP_TO_LARAVEL_80,
        //LaravelSetList::LARAVEL_CODE_QUALITY,
        //LaravelSetList::LARAVEL_COLLECTION,
    ]);
