<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Installer\DependencyInjection;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterInstallersCompilerPass implements CompilerPassInterface
{

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $schemaDefinition = $container->findDefinition('core.installer.schema_registrar');

        foreach ($container->findTaggedServiceIds('core.installer.schema') as $serviceId => $tags) {
            $schemaDefinition->addMethodCall(
                'set',
                [$serviceId, new Reference($serviceId)]
            );
        }

        $migrationDefinition = $container->findDefinition('core.installer.migration_registrar');

        foreach ($container->findTaggedServiceIds('core.installer.migration') as $serviceId => $tags) {
            $migrationDefinition->addMethodCall(
                'set',
                [$serviceId, new Reference($serviceId)]
            );
        }

        $sampleDataDefinition = $container->findDefinition('core.installer.sample_data_registrar');

        foreach ($container->findTaggedServiceIds('core.installer.sample_data') as $serviceId => $tags) {
            $sampleDataDefinition->addMethodCall(
                'set',
                [$serviceId, new Reference($serviceId)]
            );
        }
    }
}
