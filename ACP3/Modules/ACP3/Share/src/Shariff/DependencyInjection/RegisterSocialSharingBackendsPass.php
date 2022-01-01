<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\DependencyInjection;

use ACP3\Modules\ACP3\Share\Shariff\SocialSharingBackendServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterSocialSharingBackendsPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $service = $container->findDefinition(SocialSharingBackendServiceLocator::class);

        foreach ($container->findTaggedServiceIds('share.shariff.social_sharing_backend') as $serviceId => $tags) {
            $service->addMethodCall('registerService', [new Reference($serviceId)]);
        }
    }
}
