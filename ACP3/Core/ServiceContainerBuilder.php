<?php
namespace ACP3\Core;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\DataGrid\DependencyInjection\RegisterColumnRendererPass;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterSmartyPluginsPass;
use ACP3\Core\WYSIWYG\DependencyInjection\RegisterWysiwygEditorsCompilerPass;
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
     * @param string                                 $appMode
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param bool                                   $allModules
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function compileContainer($appMode, ApplicationPath $appPath, $allModules = false)
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->set('core.environment.application_path', $appPath);
        $containerBuilder->setParameter('core.environment', $appMode);

        $containerBuilder->addCompilerPass(
            new RegisterListenersPass('core.eventDispatcher', 'core.eventListener', 'core.eventSubscriber')
        );
        $containerBuilder->addCompilerPass(new RegisterSmartyPluginsPass());
        $containerBuilder->addCompilerPass(new RegisterColumnRendererPass());
        $containerBuilder->addCompilerPass(new RegisterValidationRulesPass());
        $containerBuilder->addCompilerPass(new RegisterWysiwygEditorsCompilerPass());

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
        $loader->load($appPath->getClassesDir() . 'config/services.yml');
        $loader->load($appPath->getClassesDir() . 'View/Renderer/Smarty/config/services.yml');

        // Try to get all available services
        /** @var Modules $modules */
        $modules = $containerBuilder->get('core.modules');
        $availableModules = ($allModules === true) ? $modules->getAllModules() : $modules->getInstalledModules();
        $vendors = $containerBuilder->get('core.modules.vendors')->getVendors();

        foreach ($availableModules as $module) {
            foreach ($vendors as $vendor) {
                $path = $appPath->getModulesDir() . $vendor . '/' . $module['dir'] . '/Resources/config/services.yml';

                if (is_file($path)) {
                    $loader->load($path);
                }
            }
        }

        $containerBuilder->compile();

        return $containerBuilder;
    }
}
