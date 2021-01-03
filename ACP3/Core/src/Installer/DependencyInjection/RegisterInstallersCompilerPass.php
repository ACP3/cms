<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterInstallersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $schemaDefinition = $container->findDefinition('core.installer.schema_registrar');

        foreach ($container->findTaggedServiceIds('core.installer.schema') as $serviceId => $tags) {
            $schemaDefinition->addMethodCall(
                'set',
                [new Reference($serviceId)]
            );
        }

        $this->registerMigrations($container);
        $this->registerSampleData($container);
    }

    private function registerMigrations(ContainerBuilder $container): void
    {
        $migrationServiceLocatorDefinition = $container->findDefinition('core.installer.migration_registrar');

        $locatableMigrations = [];
        foreach ($container->findTaggedServiceIds('core.installer.migration') as $serviceId => $tags) {
            $locatableMigrations[$serviceId] = new Reference($serviceId);
        }

        $migrationServiceLocatorDefinition->replaceArgument(0, $locatableMigrations);
    }

    private function registerSampleData(ContainerBuilder $container): void
    {
        $sampleDataServiceLocatorDefinition = $container->findDefinition('core.installer.sample_data_registrar');

        $locatableSampleData = [];
        foreach ($container->findTaggedServiceIds('core.installer.sample_data') as $serviceId => $tags) {
            $locatableSampleData[$serviceId] = new Reference($serviceId);
        }

        $sampleDataServiceLocatorDefinition->replaceArgument(0, $locatableSampleData);
    }
}
