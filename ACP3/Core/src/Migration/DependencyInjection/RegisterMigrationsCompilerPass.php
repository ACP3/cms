<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration\DependencyInjection;

use ACP3\Core\Migration\MigrationServiceLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterMigrationsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $migrationLocator = $container->findDefinition(MigrationServiceLocator::class);

        foreach ($container->findTaggedServiceIds('core.migration') as $serviceId => $tags) {
            $migrationLocator->addMethodCall(
                'addMigration',
                [$tags[0]['moduleName'], new Reference($serviceId)]
            );
        }
    }
}
