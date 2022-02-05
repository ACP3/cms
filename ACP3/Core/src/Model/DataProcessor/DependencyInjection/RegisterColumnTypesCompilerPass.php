<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterColumnTypesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $columnTypeStrategyLocatorDefinition = $container->findDefinition('core.model.column_type_strategy_locator');

        $locatableColumnTypeStrategies = [];
        foreach ($container->findTaggedServiceIds('core.model.column_type') as $serviceId => $tags) {
            $reference = new Reference($serviceId);
            $locatableColumnTypeStrategies[$container->getDefinition($serviceId)->getClass()] = $reference;
        }

        $columnTypeStrategyLocatorDefinition->replaceArgument(0, $locatableColumnTypeStrategies);
    }
}
