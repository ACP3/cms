<?php

declare(strict_types = 1);

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use Rector\Core\Configuration\Option;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/ACP3',
    ]);

    // Define what rule sets will be applied
    $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(SetList::PHP_73);
    $containerConfigurator->import(SetList::PHP_80);

    $services = $containerConfigurator->services();
    $services->set(\Rector\Renaming\Rector\FuncCall\RenameFunctionRector::class)->call('configure', [[\Rector\Renaming\Rector\FuncCall\RenameFunctionRector::OLD_FUNCTION_TO_NEW_FUNCTION => [
        //the_real_type
        // https://wiki.php.net/rfc/deprecations_php_7_4
        'is_real' => 'is_float',
        //apache_request_headers_function
        // https://wiki.php.net/rfc/deprecations_php_7_4
        'apache_request_headers' => 'getallheaders',
    ]]]);
    $services->set(\Rector\Php74\Rector\FuncCall\ArrayKeyExistsOnPropertyRector::class);
    $services->set(\Rector\Php74\Rector\FuncCall\FilterVarToAddSlashesRector::class);
    $services->set(\Rector\Php74\Rector\StaticCall\ExportToReflectionFunctionRector::class);
    $services->set(\Rector\Php74\Rector\FuncCall\GetCalledClassToStaticClassRector::class);
    $services->set(\Rector\Php74\Rector\FuncCall\MbStrrposEncodingArgumentPositionRector::class);
    $services->set(\Rector\Php74\Rector\Double\RealToFloatTypeCastRector::class);
    $services->set(\Rector\Php74\Rector\Assign\NullCoalescingOperatorRector::class);
    $services->set(\Rector\Php74\Rector\Function_\ReservedFnFunctionRector::class);
    $services->set(\Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector::class);
    $services->set(\Rector\Php74\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector::class);
    $services->set(\Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class);
    $services->set(\Rector\Php74\Rector\MethodCall\ChangeReflectionTypeToStringToGetNameRector::class);
    $services->set(\Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector::class);
    $services->set(\Rector\Php74\Rector\ArrayDimFetch\CurlyToSquareBracketArrayStringRector::class);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
