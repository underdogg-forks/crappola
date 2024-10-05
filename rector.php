<?php

use Rector\Arguments\Rector\FuncCall\FunctionArgumentDefaultValueReplacerRector;
use Rector\Config\RectorConfig;
use Rector\Php73\Rector\BooleanOr\IsCountableRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php80\Rector\FuncCall\ClassOnObjectRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;
use Rector\Php80\Rector\FunctionLike\UnionTypesRector;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\TypeDeclaration\Rector\FunctionLike\ReturnTypeDeclarationRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->skip([
        __DIR__ . '/application/modules/**/views/*',
        __DIR__ . '/storage',
        __DIR__ . '/uploads',
        __DIR__ . '/application/cache',
        __DIR__ . '/application/core',
        __DIR__ . '/application/helpers/country-list',
        __DIR__ . '/application/language',
        __DIR__ . '/application/logs',
        __DIR__ . '/application/third_party',
        ClassOnObjectRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
        FinalizePublicClassConstantRector::class,
        FunctionArgumentDefaultValueReplacerRector::class,
        IsCountableRector::class,
        MixedTypeRector::class,
        StringableForToStringRector::class,
        NullToStrictStringFuncCallArgRector::class,
        UnionTypesRector::class,
    ]);
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/src',
        __DIR__ . '/Modules',
    ]);
    $rectorConfig->rule(
        ReturnTypeDeclarationRector::class
    );
};
