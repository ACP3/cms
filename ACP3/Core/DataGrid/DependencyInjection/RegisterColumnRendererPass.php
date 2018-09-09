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
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('core.data_grid.data_grid');

        foreach ($container->findTaggedServiceIds('core.data_grid.column_renderer') as $serviceId => $tags) {
            $definition->addMethodCall('registerColumnRenderer', [new Reference($serviceId)]);
        }
        foreach ($container->findTaggedServiceIds('core.helpers.data_grid.column_renderer') as $serviceId => $tags) {
            $definition->addMethodCall('registerColumnRenderer', [new Reference($serviceId)]);
        }
    }
}
