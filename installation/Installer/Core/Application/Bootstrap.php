<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Application;

use ACP3\Core;
use ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Bootstrap extends Core\Application\AbstractBootstrap
{
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath ApplicationPath
     */
    protected $appPath;

    /**
     * {@inheritdoc}
     */
    public function handle(SymfonyRequest $symfonyRequest, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->setErrorHandler();
        $this->initializeClasses($symfonyRequest);

        return $this->outputPage();
    }

    /**
     * {@inheritdoc}
     */
    public function startupChecks()
    {
        // Standardzeitzone festlegen
        \date_default_timezone_set('UTC');

        if ($this->appMode === Core\Environment\ApplicationMode::UPDATER) {
            return $this->databaseConfigExists();
        }

        return true;
    }

    protected function initializeApplicationPath()
    {
        $this->appPath = new ApplicationPath($this->appMode);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function initializeClasses(SymfonyRequest $symfonyRequest)
    {
        $this->container = ServiceContainerBuilder::create($this->logger, $this->appPath, $symfonyRequest, $this->appMode);
    }

    /**
     * {@inheritdoc}
     */
    public function outputPage()
    {
        /** @var \ACP3\Core\Http\RedirectResponse $redirect */
        $redirect = $this->container->get('core.http.redirect_response');

        try {
            /** @var \ACP3\Core\Application\ControllerActionDispatcher $controllerActionDispatcher */
            $controllerActionDispatcher = $this->container->get('core.application.controller_action_dispatcher');

            $response = $controllerActionDispatcher->dispatch();
        } catch (Core\Controller\Exception\ControllerActionNotFoundException $e) {
            $response = $redirect->temporary('errors/index/not_found');
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $response = $redirect->temporary('errors/index/server_error');
        }

        return $response;
    }
}
