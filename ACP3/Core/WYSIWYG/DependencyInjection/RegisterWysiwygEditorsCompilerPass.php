<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\WYSIWYG\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterWysiwygEditorsCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('core.wysiwyg.wysiwyg_editor_registrar');
        $plugins = $container->findTaggedServiceIds('core.wysiwyg.editor');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall(
                'registerWysiwygEditor',
                [$serviceId, new Reference($serviceId)]
            );
        }
    }
}
