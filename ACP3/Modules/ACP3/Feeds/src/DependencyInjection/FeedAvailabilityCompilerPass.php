<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\DependencyInjection;

use ACP3\Modules\ACP3\Feeds\Utility\FeedAvailabilityRegistrar;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FeedAvailabilityCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(FeedAvailabilityRegistrar::class);
        $plugins = $container->findTaggedServiceIds('feeds.extension.feed_availability');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall(
                'registerModule',
                [new Reference($serviceId)]
            );
        }
    }
}
