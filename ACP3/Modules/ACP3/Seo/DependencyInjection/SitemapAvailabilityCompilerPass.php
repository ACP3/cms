<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SitemapAvailabilityCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('seo.utility.sitemap_availability_registrar');
        $plugins = $container->findTaggedServiceIds('seo.extension.sitemap_availability');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall(
                'registerModule',
                [new Reference($serviceId)]
            );
        }
    }
}
