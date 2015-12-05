<?php
namespace ACP3\Core;

use ACP3\Core\Helpers\DataGrid\DependencyInjection\RegisterColumnRendererPass;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterPluginsPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * Class ServiceContainerBuilder
 * @package ACP3\Core
 */
class ServiceContainerBuilder
{
    /**
     * @param string $environment
     * @param bool   $allModules
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function compileContainer($environment, $allModules = false)
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addCompilerPass(new RegisterListenersPass('core.eventDispatcher', 'core.eventListener', 'core.eventSubscriber'));
        $containerBuilder->addCompilerPass(new RegisterPluginsPass());
        $containerBuilder->addCompilerPass(new RegisterColumnRendererPass());
        $containerBuilder->addCompilerPass(new RegisterValidationRulesPass());

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
        $loader->load(CLASSES_DIR . 'config/services.yml');
        $loader->load(CLASSES_DIR . 'View/Renderer/Smarty/config/services.yml');

        $containerBuilder->setParameter('core.environment', $environment);

        // Try to get all available services
        /** @var Modules $modules */
        $modules = $containerBuilder->get('core.modules');
        $availableModules = ($allModules === true) ? $modules->getAllModules() : $modules->getInstalledModules();
        $vendors = $containerBuilder->get('core.modules.vendors')->getVendors();

        foreach ($availableModules as $module) {
            foreach ($vendors as $vendor) {
                $path = MODULES_DIR . $vendor . '/' . $module['dir'] . '/Resources/config/services.yml';

                if (is_file($path)) {
                    $loader->load($path);
                }
            }
        }

        $containerBuilder->compile();

        return $containerBuilder;
    }
}