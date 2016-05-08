<?php
namespace ACP3\Installer\Core;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterSmartyPluginsPass;
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
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    private static $appPath;
    /**
     * @var string
     */
    private static $appMode;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private static $container;

    /**
     * @param string                                           $appMode
     * @param \ACP3\Installer\Core\Environment\ApplicationPath $appPath
     * @param bool                                             $includeModules
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function compileContainer($appMode, ApplicationPath $appPath, $includeModules = false)
    {
        self::$appMode = $appMode;
        self::$appPath = $appPath;
        self::$container = new ContainerBuilder();

        self::$container->setParameter('cache_driver', 'Array');
        self::$container->setParameter('core.environment', self::$appMode);
        self::$container->set('core.environment.application_path', self::$appPath);
        self::$container->addCompilerPass(
            new RegisterListenersPass('core.eventDispatcher', 'core.eventListener', 'core.eventSubscriber')
        );
        self::$container->addCompilerPass(new RegisterSmartyPluginsPass());
        self::$container->addCompilerPass(new RegisterValidationRulesPass());

        $loader = new YamlFileLoader(self::$container, new FileLocator(__DIR__));

        if (self::canIncludeModules($includeModules) === true) {
            $loader->load(self::$appPath->getClassesDir() . 'config/services.yml');
        }

        $loader->load(self::$appPath->getInstallerClassesDir() . 'config/services.yml');
        if (self::$appMode === ApplicationMode::UPDATER) {
            $loader->load(self::$appPath->getInstallerClassesDir() . 'config/update.yml');
        }

        self::includeModules($loader, $includeModules);

        self::$container->compile();

        return self::$container;
    }

    /**
     * @param boolean $includeModules
     *
     * @return bool
     */
    protected static function canIncludeModules($includeModules)
    {
        return self::$appMode === ApplicationMode::UPDATER || $includeModules === true;
    }

    /**
     * @param YamlFileLoader $loader
     * @param boolean        $includeModules
     */
    protected static function includeModules(YamlFileLoader $loader, $includeModules)
    {
        if (self::canIncludeModules($includeModules) === true) {
            // Ugly hack to prevent request override from included ACP3 modules
            $request = self::$container->get('core.http.request');

            $vendors = self::$container->get('core.modules.vendors')->getVendors();
            foreach ($vendors as $vendor) {
                $namespaceModules = glob(self::$appPath->getModulesDir() . $vendor . '/*/Resources/config/services.yml');
                foreach ($namespaceModules as $module) {
                    $loader->load($module);
                }
            }

            self::$container->set('core.http.request', $request);
        }
    }
}
