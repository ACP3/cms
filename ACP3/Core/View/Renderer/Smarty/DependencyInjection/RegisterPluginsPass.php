<?php
namespace ACP3\Core\View\Renderer\Smarty\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        $definition = $container->findDefinition('core.view.renderer.smarty');

        $plugins = $container->findTaggedServiceIds('smarty.plugin');

        foreach ($plugins as $plugin) {
            $definition->addMethodCall()
        }

        $services = $this->container->getServiceIds();
        foreach ($services as $serviceName) {
            if (strpos($serviceName, 'smarty.plugin.') === 0) {
                /** @var AbstractPlugin $plugin */
                $plugin = $this->container->get($serviceName);
                $plugin->registerPlugin($this->renderer);
            } elseif (strpos($serviceName, 'smarty.filter.') === 0) {
                /** @var AbstractFilter $filter */
                $filter = $this->container->get($serviceName);
                $filter->registerFilter($this->renderer);
            } elseif (strpos($serviceName, 'smarty.resource.') === 0) {
                /** @var AbstractResource $resource */
                $resource = $this->container->get($serviceName);
                $resource->registerResource($this->renderer);
            }
        }

    }
}