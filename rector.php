<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedPropertyRector;

return RectorConfig::configure()
    ->withSkip([
        'app/Providers/AppServiceProvider.php',
        Rector\Arguments\Rector\FuncCall\FunctionArgumentDefaultValueReplacerRector::class,
        Rector\Php73\Rector\BooleanOr\IsCountableRector::class,
        Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class,
        Rector\Php80\Rector\Class_\StringableForToStringRector::class,
        Rector\Php80\Rector\FunctionLike\MixedTypeRector::class,
        Rector\Php80\Rector\FuncCall\ClassOnObjectRector::class,
        Rector\Php81\Rector\Class_\SpatieEnumClassToEnumRector::class,
        Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector::class,
    ])
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/Modules',
    ])
    ->withImportNames()
    ->withRules([
        ReturnTypeFromStrictTypedPropertyRector::class,
    ])
    ->withSets([
        //SetList::DEAD_CODE,
        //SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        //SetList::EARLY_RETURN,
        //SetList::TYPE_DECLARATION,
        //SetList::CARBON,
        //LaravelSetList::LARAVEL_CODE_QUALITY,
        //LaravelSetList::LARAVEL_COLLECTION,
        //LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        //LaravelLevelSetList::UP_TO_LARAVEL_100,
        //LevelSetList::UP_TO_PHP_81,
    ]);
