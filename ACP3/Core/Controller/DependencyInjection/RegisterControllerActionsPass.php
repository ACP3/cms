<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterControllerActionsPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $service = $container->findDefinition('core.controller.controller_action_locator');

        $locatableControllerActions = [];
        foreach ($container->findTaggedServiceIds('acp3.controller.action') as $serviceId => $tags) {
            $locatableControllerActions[$serviceId] = new Reference($serviceId);
        }

        $service->replaceArgument(0, $locatableControllerActions);
    }
}
