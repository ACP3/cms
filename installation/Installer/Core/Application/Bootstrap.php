<?php

namespace ACP3\Installer\Core\Application;

use ACP3\Core;
use ACP3\Installer\Core\Environment\ApplicationPath;
use ACP3\Installer\Core\ServiceContainerBuilder;

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
        if ($this->startupChecks() === false) {
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
        $this->container = ServiceContainerBuilder::compileContainer($this->appMode, $this->appPath);
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
        $redirect = $this->container->get('core.redirect');

        try {
            $this->container->get('core.application.front_controller')->dispatch();
        } catch (Core\Exceptions\ControllerActionNotFound $e) {
            $redirect->temporary('errors/index/not_found')->send();
        } catch (\Exception $e) {
            $this->container->get('core.logger')->critical('installer', $e->getMessage());
            $redirect->temporary('errors/index/server_error')->send();
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
