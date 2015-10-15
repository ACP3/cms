<?php

namespace ACP3\Installer\Core;

use ACP3\Core;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class Application
 * @package ACP3\Installer
 */
class Application extends Core\AbstractApplication
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->defineDirConstants();

        if ($this->environment === Core\Enum\Environment::UPDATER && $this->startupChecks() === false) {
            return;
        }

        $this->setErrorHandler();
        $this->initializeClasses();
        $this->outputPage();
    }

    /**
     * @inheritdoc
     */
    public function defineDirConstants()
    {
        define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
        $phpSelf = dirname(PHP_SELF);
        define('ROOT_DIR', substr($phpSelf !== '/' ? $phpSelf . '/' : '/', 0, -13));
        define('INSTALLER_ROOT_DIR', substr(PHP_SELF, 0, strrpos(PHP_SELF, '/') + 1));
        define('ACP3_DIR', ACP3_ROOT_DIR . 'ACP3/');
        define('CLASSES_DIR', ACP3_DIR . 'Core/');
        define('MODULES_DIR', ACP3_DIR . 'Modules/');
        define('UPLOADS_DIR', ACP3_ROOT_DIR . 'uploads/');
        define('CACHE_DIR', ACP3_ROOT_DIR . 'cache/');

        define('INSTALLER_ACP3_DIR', realpath(ACP3_DIR . '../installation/') . '/Installer/');
        define('INSTALLER_MODULES_DIR', INSTALLER_ACP3_DIR . 'Modules/');
        define('INSTALLER_CLASSES_DIR', INSTALLER_ACP3_DIR . 'Core/');
        define('INSTALLER_CACHE_DIR', CACHE_DIR . 'install/');
        define('INSTALLATION_DIR', ACP3_ROOT_DIR . 'installation/');

        // Set theme paths
        define('DESIGN_PATH', INSTALLATION_DIR . 'design/');
        define('DESIGN_PATH_INTERNAL', INSTALLATION_DIR . 'design/');
    }

    /**
     * @inheritdoc
     */
    public function initializeClasses()
    {
        $this->container = new ContainerBuilder();

        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));

        if ($this->environment === Core\Enum\Environment::UPDATER) {
            $loader->load(INSTALLER_CLASSES_DIR . 'config/update.yml');
        } else {
            $loader->load(INSTALLER_CLASSES_DIR . 'config/services.yml');
        }

        $this->container->setParameter('core.environment', $this->environment);

        // When in updater context, also include "normal" module services
        if ($this->environment === Core\Enum\Environment::UPDATER) {
            $vendors = $this->container->get('core.modules.vendors')->getVendors();

            foreach ($vendors as $vendor) {
                $namespaceModules = glob(MODULES_DIR . $vendor . '/*/Resources/config/services.yml');
                foreach ($namespaceModules as $module) {
                    $loader->load($module);
                }
            }
        }

        $this->container->get('core.view')->setRenderer('smarty', ['compile_id' => 'installer']);

        $this->container->compile();
    }

    /**
     * @inheritdoc
     */
    public function outputPage()
    {
        $request = $this->container->get('core.request');
        $redirect = $this->container->get('core.redirect');

        $frontController = new FrontController($this->container);

        try {
            $serviceId = $request->getModule() . '.controller.install.' . $request->getController();
            $frontController->dispatch($serviceId, $request->getControllerAction());
        } catch (Core\Exceptions\ControllerActionNotFound $e) {
            $redirect->temporary('errors/index/404')->send();
        } catch (\Exception $e) {
            Core\Logger::critical('installer', $e->getMessage());
            $redirect->temporary('errors/index/500')->send();
        }
    }

    /**
     * @inheritdoc
     */
    public function startupChecks()
    {
        // Standardzeitzone festlegen
        date_default_timezone_set('UTC');

        if ($this->environment === Core\Enum\Environment::UPDATER) {
            return $this->databaseConfigExists();
        }

        return true;
    }

}
