<?php

namespace ACP3\Installer\Core\Application;

use ACP3\Core;
use ACP3\Installer\Core\Environment\ApplicationPath;
use ACP3\Installer\Core\FrontController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class Bootstrap
 * @package ACP3\Installer\Core\Application
 */
class Bootstrap extends Core\Application\AbstractBootstrap
{
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath ApplicationPath
     */
    protected $appPath;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->appMode === Core\Environment\ApplicationMode::UPDATER && $this->startupChecks() === false) {
            return;
        }

        $this->setErrorHandler();
        $this->initializeClasses();
        $this->outputPage();
    }

    /**
     * @param string $appMode
     */
    protected function setAppPath($appMode)
    {
        $this->appPath = new ApplicationPath($appMode);
    }

    /**
     * @inheritdoc
     */
    public function initializeClasses()
    {
        $this->container = new ContainerBuilder();
        $this->container->setDefinition('core.environment.application_path',
            new Definition(ApplicationPath::class, [$this->appMode]));
        $this->container->addCompilerPass(new Core\View\Renderer\Smarty\DependencyInjection\RegisterPluginsPass());

        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));

        if ($this->appMode === Core\Environment\ApplicationMode::UPDATER) {
            $loader->load('../config/update.yml');
        } else {
            $loader->load('../config/services.yml');
        }

        $this->container->setParameter('core.environment', $this->appMode);

        // When in updater context, also include "normal" module services
        if ($this->appMode === Core\Environment\ApplicationMode::UPDATER) {
            $vendors = $this->container->get('core.modules.vendors')->getVendors();

            foreach ($vendors as $vendor) {
                $namespaceModules = glob($this->appPath->getModulesDir() . $vendor . '/*/Resources/config/services.yml');
                foreach ($namespaceModules as $module) {
                    $loader->load($module);
                }
            }
        }

        $this->container->compile();

        $this->appPath = $this->container->get('core.environment.application_path');
    }

    private function applyThemePaths()
    {
        $this->appPath
            ->setDesignPathWeb($this->appPath->getInstallerWebRoot() . 'design/')
            ->setDesignPathInternal(ACP3_ROOT_DIR . 'installation/design/');
    }

    /**
     * @inheritdoc
     */
    public function outputPage()
    {
        $this->applyThemePaths();
        $request = $this->container->get('core.request');
        $redirect = $this->container->get('core.redirect');

        $frontController = new FrontController($this->container);

        try {
            $serviceId = $request->getModule() . '.controller.install.' . $request->getController();
            $frontController->dispatch($serviceId, $request->getControllerAction());
        } catch (Core\Exceptions\ControllerActionNotFound $e) {
            $redirect->temporary('errors/index/404')->send();
        } catch (\Exception $e) {
            $this->container->get('core.logger')->critical('installer', $e->getMessage());
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

        if ($this->appMode === Core\Environment\ApplicationMode::UPDATER) {
            return $this->databaseConfigExists();
        }

        return true;
    }

}
