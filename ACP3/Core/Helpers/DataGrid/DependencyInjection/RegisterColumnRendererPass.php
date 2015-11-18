<?php
namespace ACP3\Core\Helpers\DataGrid\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterColumnRendererPass
 * @package ACP3\Core\Helpers\DataGrid\DependencyInjection
 */
class RegisterColumnRendererPass implements CompilerPassInterface
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
        $definition = $container->findDefinition('core.helpers.data_grid');
        $columnRenderer = $container->findTaggedServiceIds('core.helpers.data_grid.column_renderer');

        foreach ($columnRenderer as $serviceId => $tags) {
            $definition->addMethodCall('registerColumnRenderer', [new Reference($serviceId)]);
        }
    }
}