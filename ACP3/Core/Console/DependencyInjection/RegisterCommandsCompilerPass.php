<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Console\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterCommandsCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('symfony_console');
        $plugins = $container->findTaggedServiceIds('acp3.console.command');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall(
                'add',
                [new Reference($serviceId)]
            );
        }
    }
}
