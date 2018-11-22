<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @deprecated since version 4.33.0, to be removed with version 5.0.0
 */
class RegisterLegacySmartyPluginsPass implements CompilerPassInterface
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
        $definition = $container->findDefinition('core.view.renderer.smarty');
        $plugins = $container->findTaggedServiceIds('core.view.extension');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall('registerSmartyPlugin', [new Reference($serviceId)]);
        }
    }
}
