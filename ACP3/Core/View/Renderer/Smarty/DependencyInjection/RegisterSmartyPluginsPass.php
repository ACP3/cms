<?php
namespace ACP3\Core\View\Renderer\Smarty\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterSmartyPluginsPass
 * @package ACP3\Core\View\Renderer\Smarty\DependencyInjection
 */
class RegisterSmartyPluginsPass implements CompilerPassInterface
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
        $definition = $container->findDefinition('core.view.renderer.smarty');
        $plugins = $container->findTaggedServiceIds('core.view.extension');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall('registerSmartyPlugin', [new Reference($serviceId)]);
        }
    }
}
