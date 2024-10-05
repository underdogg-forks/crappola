<?php

use Rector\Arguments\Rector\FuncCall\FunctionArgumentDefaultValueReplacerRector;
use Rector\Config\RectorConfig;
use Rector\Php73\Rector\BooleanOr\IsCountableRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php80\Rector\FuncCall\ClassOnObjectRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;
use Rector\Php81\Rector\Class_\SpatieEnumClassToEnumRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use RectorLaravel\Rector\Class_\AnonymousMigrationsRector;
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
        SpatieEnumClassToEnumRector::class,
    ])
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/Modules',
    ])
    ->withRules([
        //AnonymousMigrationsRector::class,
        //AddReturnTypeDeclarationRector::class,
    ])
    ->withSets([
        //LevelSetList::UP_TO_PHP_81,
        //SetList::TYPE_DECLARATION,
        //SetList::EARLY_RETURN,
        //SetList::CARBON,
        //SetList::CODE_QUALITY,
        //SetList::CODING_STYLE,
        //SetList::DEAD_CODE,
        //LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        //LaravelLevelSetList::UP_TO_LARAVEL_80,
        //LaravelSetList::LARAVEL_CODE_QUALITY,
        //LaravelSetList::LARAVEL_COLLECTION,
    ]);
