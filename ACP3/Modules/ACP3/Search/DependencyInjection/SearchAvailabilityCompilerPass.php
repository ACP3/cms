<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Search\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SearchAvailabilityCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('search.utility.search_availability_registrar');
        $plugins = $container->findTaggedServiceIds('search.extension.search_availability');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall(
                'registerModule',
                [new Reference($serviceId)]
            );
        }
    }
}
