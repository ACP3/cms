<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterSmartyPluginsPass implements CompilerPassInterface
{
    /**
     * @var Reference[]
     */
    private $locatablePlugins = [];

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('core.view.renderer.smarty');

        $this->registerBlocks($definition, $container);
        $this->registerFilters($definition, $container);
        $this->registerFunctions($definition, $container);
        $this->registerModifiers($definition, $container);
        $this->registerResources($definition, $container);

        $service = $container->findDefinition('core.view.renderer.smarty.plugin_service_locator');
        $service->replaceArgument(0, $this->locatablePlugins);
    }

    private function registerBlocks(Definition $smartyDefinition, ContainerBuilder $container): void
    {
        $blocks = $container->findTaggedServiceIds('smarty.plugin.block');

        foreach ($blocks as $serviceId => $tags) {
            $smartyDefinition->addMethodCall(
                'registerBlock',
                [$tags[0]['pluginName'], $serviceId]
            );

            $this->locatablePlugins[$serviceId] = new Reference($serviceId);
        }
    }

    private function registerFilters(Definition $smartyDefinition, ContainerBuilder $container): void
    {
        $filters = $container->findTaggedServiceIds('smarty.plugin.filter');

        foreach ($filters as $serviceId => $tags) {
            $smartyDefinition->addMethodCall(
                'registerFilter',
                [$tags[0]['filterType'], $serviceId]
            );

            $this->locatablePlugins[$serviceId] = new Reference($serviceId);
        }
    }

    private function registerFunctions(Definition $smartyDefinition, ContainerBuilder $container): void
    {
        $functions = $container->findTaggedServiceIds('smarty.plugin.function');

        foreach ($functions as $serviceId => $tags) {
            $smartyDefinition->addMethodCall(
                'registerFunction',
                [$tags[0]['pluginName'], $serviceId]
            );

            $this->locatablePlugins[$serviceId] = new Reference($serviceId);
        }
    }

    private function registerModifiers(Definition $smartyDefinition, ContainerBuilder $container): void
    {
        $modifiers = $container->findTaggedServiceIds('smarty.plugin.modifier');

        foreach ($modifiers as $serviceId => $tags) {
            $smartyDefinition->addMethodCall(
                'registerModifier',
                [$tags[0]['pluginName'], $serviceId]
            );

            $this->locatablePlugins[$serviceId] = new Reference($serviceId);
        }
    }

    private function registerResources(Definition $smartyDefinition, ContainerBuilder $container): void
    {
        $resources = $container->findTaggedServiceIds('smarty.plugin.resource');

        foreach ($resources as $serviceId => $tags) {
            $smartyDefinition->addMethodCall(
                'registerResource',
                [$tags[0]['pluginName'], new Reference($serviceId)]
            );

            $this->locatablePlugins[$serviceId] = new Reference($serviceId);
        }
    }
}
