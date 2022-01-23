<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\DependencyInjection;

use ACP3\Core\Assets\Libraries;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterAssetLibraryPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $libraries = $container->findDefinition(Libraries::class);

        $librariesToAdd = $container->findTaggedServiceIds('acp3.assets.library');

        foreach ($librariesToAdd as $serviceId => $attributes) {
            $libraries->addMethodCall('addLibrary', [new Reference($serviceId)]);
        }
    }
}
