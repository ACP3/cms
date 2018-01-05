<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Authentication\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterAuthenticationsCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('core.authentication.authentication_registrar');

        foreach ($container->findTaggedServiceIds('core.authentication') as $serviceId => $tags) {
            $definition->addMethodCall(
                'set',
                [$serviceId, new Reference($serviceId)]
            );
        }
    }
}
