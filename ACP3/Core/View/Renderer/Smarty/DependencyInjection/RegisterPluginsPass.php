<?php
namespace ACP3\Core\View\Renderer\Smarty\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterPluginsPass
 * @package ACP3\Core\View\Renderer\Smarty\DependencyInjection
 */
class RegisterPluginsPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('core.view');
        $plugins = $container->findTaggedServiceIds('core.view.extension');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall('registerPlugin', [new Reference($serviceId)]);
        }
    }
}