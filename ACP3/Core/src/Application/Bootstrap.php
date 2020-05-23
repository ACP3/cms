<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Event\OutputPageExceptionEvent;
use ACP3\Core\Application\Exception\MaintenanceModeActiveException;
use ACP3\Core\Controller\Exception\ForwardControllerActionAwareExceptionInterface;
use ACP3\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Core\Environment\ApplicationMode;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bootstraps the application.
 */
class Bootstrap extends AbstractBootstrap
{
    private $containerName = 'ACP3ServiceContainer';

    /**
     * {@inheritdoc}
     *
     * @throws \Throwable
     */
    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->setErrorHandler();
        $this->initializeClasses($request);

        return $this->outputPage();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function initializeClasses(SymfonyRequest $symfonyRequest): void
    {
        $file = $this->appPath->getCacheDir() . 'container.php';
        $cache = new ConfigCache($file, ($this->appMode === ApplicationMode::DEVELOPMENT));

        $this->dumpContainer($symfonyRequest, $cache);

        require_once $cache->getPath();

        $this->container = new $this->containerName();
        $this->container->set('core.environment.application_path', $this->appPath);
        $this->container->set('core.http.symfony_request', $symfonyRequest);
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function dumpContainer(SymfonyRequest $symfonyRequest, ConfigCache $cache)
    {
        if (!$cache->isFresh()) {
            $containerBuilder = ServiceContainerBuilder::create(
                $this->appPath, $symfonyRequest
            );

            $dumper = new PhpDumper($containerBuilder);
            $cache->write(
                $dumper->dump([
                    'class' => 'ACP3ServiceContainer',
                    'debug' => $this->appMode === ApplicationMode::DEVELOPMENT,
                ]),
                $containerBuilder->getResources()
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Throwable
     */
    public function outputPage(): Response
    {
        /** @var \ACP3\Core\Application\ControllerActionDispatcher $controllerActionDispatcher */
        $controllerActionDispatcher = $this->container->get('core.application.controller_action_dispatcher');

        try {
            /** @var \ACP3\Core\Application\ControllerActionDispatcher $controllerActionDispatcher */
            $controllerActionDispatcher = $this->container->get('core.application.controller_action_dispatcher');

            $response = $controllerActionDispatcher->dispatch();
        } catch (ForwardControllerActionAwareExceptionInterface $e) {
            $response = $controllerActionDispatcher->dispatch($e->getServiceId(), $e->routeParams());
        } catch (MaintenanceModeActiveException $e) {
            $response = new Response($e->getMessage(), $e->getCode());
        } catch (\Throwable $e) {
            /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $this->container->get('core.event_dispatcher');

            $eventDispatcher->dispatch(new OutputPageExceptionEvent($e), OutputPageExceptionEvent::NAME);

            throw $e;
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function isInstalled(): bool
    {
        \date_default_timezone_set('UTC');

        return $this->databaseConfigExists();
    }
}
