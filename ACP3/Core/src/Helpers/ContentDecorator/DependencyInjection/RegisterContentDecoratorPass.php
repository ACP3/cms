<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\ContentDecorator\DependencyInjection;

use ACP3\Core\Helpers\ContentDecorator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterContentDecoratorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(ContentDecorator::class);

        foreach ($container->findTaggedServiceIds('core.content_decorator') as $serviceId => $tags) {
            $definition->addMethodCall('registerContentDecorator', [new Reference($serviceId)]);
        }
    }
}
