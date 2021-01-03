<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Authentication\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterAuthenticationsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $authenticationLocatorDefinition = $container->findDefinition('core.authentication.authentication_registrar');

        $locatableAuthentications = [];
        foreach ($container->findTaggedServiceIds('core.authentication') as $serviceId => $tags) {
            $locatableAuthentications[$serviceId] = new Reference($serviceId);
        }

        $authenticationLocatorDefinition->replaceArgument(0, $locatableAuthentications);
    }
}
