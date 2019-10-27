<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Application;

use ACP3\Core;
use ACP3\Core\Application\Event\OutputPageExceptionEvent;
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
        $this->container = ServiceContainerBuilder::create($this->appPath, $symfonyRequest, $this->appMode);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \ACP3\Core\Controller\Exception\ControllerActionNotFoundException
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \ReflectionException
     */
    public function outputPage()
    {
        /** @var \ACP3\Core\Application\ControllerActionDispatcher $controllerActionDispatcher */
        $controllerActionDispatcher = $this->container->get('core.application.controller_action_dispatcher');

        try {
            $response = $controllerActionDispatcher->dispatch();
        } catch (Core\Controller\Exception\ControllerActionNotFoundException $e) {
            $response = $controllerActionDispatcher->dispatch('errors.controller.install.index.not_found');
        } catch (\Throwable $e) {
            /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $this->container->get('core.event_dispatcher');

            $eventDispatcher->dispatch(new OutputPageExceptionEvent($e), 'core.output_page_exception');

            $response = $controllerActionDispatcher->dispatch('errors.controller.install.index.server_error');
        }

        return $response;
    }
}
