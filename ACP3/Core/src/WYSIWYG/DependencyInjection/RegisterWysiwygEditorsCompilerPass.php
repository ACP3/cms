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
     */
    public function process(ContainerBuilder $container): void
    {
        $editorLocatorDefinition = $container->findDefinition('core.wysiwyg.wysiwyg_editor_registrar');

        $locatableEditors = [];
        foreach ($container->findTaggedServiceIds('core.wysiwyg.editor') as $serviceId => $tags) {
            $locatableEditors[$serviceId] = new Reference($serviceId);
        }

        $editorLocatorDefinition->replaceArgument(0, $locatableEditors);
    }
}
