<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Cache\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterCacheItemPoolsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $service = $container->findDefinition('core.cache.cache_pool_service_locator');

        $locatableCacheItemPools = [];
        foreach ($container->findTaggedServiceIds('acp3.cache_item_pool') as $serviceId => $tags) {
            $locatableCacheItemPools[$serviceId] = new Reference($serviceId);
        }

        $service->replaceArgument(0, $locatableCacheItemPools);
    }
}
