<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterValidationRulesPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $plugins = $container->findTaggedServiceIds('core.validation.validation_rule');

        $serviceLocator = $container->findDefinition('core.validator.validation_rule_locator');
        $locatableServices = [];

        foreach ($plugins as $serviceId => $tags) {
            $validationRule = $container->findDefinition($serviceId);

            $locatableServices[$validationRule->getClass()] = new Reference($serviceId);
        }

        $serviceLocator->replaceArgument(0, $locatableServices);
    }
}
