<?php

declare(strict_types = 1);

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\ArrayDimFetch\CurlyToSquareBracketArrayStringRector;
use Rector\Php74\Rector\Assign\NullCoalescingOperatorRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php74\Rector\Double\RealToFloatTypeCastRector;
use Rector\Php74\Rector\FuncCall\ArrayKeyExistsOnPropertyRector;
use Rector\Php74\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Php74\Rector\FuncCall\FilterVarToAddSlashesRector;
use Rector\Php74\Rector\FuncCall\MbStrrposEncodingArgumentPositionRector;
use Rector\Php74\Rector\Function_\ReservedFnFunctionRector;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Php74\Rector\MethodCall\ChangeReflectionTypeToStringToGetNameRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Php74\Rector\StaticCall\ExportToReflectionFunctionRector;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/ACP3',
    ]);
    $rectorConfig->parallel();
    $rectorConfig->cacheDirectory(__DIR__ . '/.rector-cache');

    // Define what rule sets will be applied
    $rectorConfig->import(SetList::DEAD_CODE);
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);

    $services = $rectorConfig->services();
    $services->set(RenameFunctionRector::class)->call('configure', [[
        // the_real_type
        // https://wiki.php.net/rfc/deprecations_php_7_4
        'is_real' => 'is_float',
        // apache_request_headers_function
        // https://wiki.php.net/rfc/deprecations_php_7_4
        'apache_request_headers' => 'getallheaders',
    ]]);
    $services->set(ArrayKeyExistsOnPropertyRector::class);
    $services->set(FilterVarToAddSlashesRector::class);
    $services->set(ExportToReflectionFunctionRector::class);
    $services->set(MbStrrposEncodingArgumentPositionRector::class);
    $services->set(RealToFloatTypeCastRector::class);
    $services->set(NullCoalescingOperatorRector::class);
    $services->set(ReservedFnFunctionRector::class);
    $services->set(ClosureToArrowFunctionRector::class);
    $services->set(ArraySpreadInsteadOfArrayMergeRector::class);
    $services->set(AddLiteralSeparatorToNumberRector::class);
    $services->set(ChangeReflectionTypeToStringToGetNameRector::class);
    $services->set(RestoreDefaultNullToNullableTypePropertyRector::class);
    $services->set(CurlyToSquareBracketArrayStringRector::class);

    $rectorConfig->skip([
        FinalizePublicClassConstantRector::class,
    ]);
};
