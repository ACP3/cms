<?php
namespace ACP3\Core\Validator\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterValidationRulesPass
 * @package ACP3\Core\Validator\DependencyInjection
 */
class RegisterValidationRulesPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('core.validator');
        $plugins = $container->findTaggedServiceIds('core.validator.validation_rule');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall('registerValidationRule', [new Reference($serviceId)]);
        }
    }
}