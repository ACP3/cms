<?php

namespace ACP3\Installer\Core\Application;

use ACP3\Core;
use ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Installer\Core\Environment\ApplicationPath;

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
        $this->container = ServiceContainerBuilder::create($this->appMode, $this->appPath);
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
        $redirect = $this->container->get('core.http.redirect_response');

        try {
            $this->container->get('core.application.controller_resolver')->dispatch();
        } catch (Core\Controller\Exception\ControllerActionNotFoundException $e) {
            $redirect->temporary('errors/index/not_found')->send();
        } catch (\Exception $e) {
            $this->container->get('core.logger')->critical('installer', $e->getMessage());
            $redirect->temporary('errors/index/server_error')->send();
        }
    }
}
