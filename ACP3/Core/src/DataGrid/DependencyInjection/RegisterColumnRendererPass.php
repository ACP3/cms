<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterColumnRendererPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $serviceLocator = $container->findDefinition('core.data_grid.column_renderer_locator');

        $locatableServices = [];
        foreach ($container->findTaggedServiceIds('core.data_grid.column_renderer') as $serviceId => $tags) {
            $columnRendererDefinition = $container->findDefinition($serviceId);

            $locatableServices[$columnRendererDefinition->getClass()] = new Reference($serviceId);
        }

        $serviceLocator->replaceArgument(0, $locatableServices);
    }
}
