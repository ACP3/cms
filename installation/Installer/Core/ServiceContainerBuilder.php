<?php
namespace ACP3\Installer\Core;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterPluginsPass;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * Class ServiceContainerBuilder
 * @package ACP3\Installer\Core
 */
class ServiceContainerBuilder
{
    /**
     * @param string                                           $appMode
     * @param \ACP3\Installer\Core\Environment\ApplicationPath $appPath
     * @param bool                                             $includeModules
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function compileContainer($appMode, ApplicationPath $appPath, $includeModules = false)
    {
        $container = new ContainerBuilder();

        $container->setParameter('cache_driver', 'Array');
        $container->setParameter('core.environment', $appMode);
        $container->set('core.environment.application_path', $appPath);
        $container->addCompilerPass(
            new RegisterListenersPass('core.eventDispatcher', 'core.eventListener', 'core.eventSubscriber')
        );
        $container->addCompilerPass(new RegisterPluginsPass());
        $container->addCompilerPass(new RegisterValidationRulesPass());

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));

        if ($appMode === ApplicationMode::UPDATER) {
            $loader->load('./config/update.yml');
        } else {
            $loader->load('./config/services.yml');
        }

        if ($appMode === ApplicationMode::UPDATER || $includeModules === true) {
            $vendors = $container->get('core.modules.vendors')->getVendors();

            foreach ($vendors as $vendor) {
                $namespaceModules = glob($appPath->getModulesDir() . $vendor . '/*/Resources/config/services.yml');
                foreach ($namespaceModules as $module) {
                    $loader->load($module);
                }
            }
        }

        $container->compile();

        return $container;
    }
}