<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @deprecated since version 4.30.0, to be removed with 5.0.0. Use ACP3\Core\DataGrid\DependencyInjection\RegisterColumnRendererPass instead
 */
class RegisterColumnRendererPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('core.helpers.data_grid');

        foreach ($container->findTaggedServiceIds('core.helpers.data_grid.column_renderer') as $serviceId => $tags) {
            $definition->addMethodCall('registerColumnRenderer', [new Reference($serviceId)]);
        }
    }
}
