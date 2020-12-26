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
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function initializeClasses(): void
    {
        $file = $this->appPath->getCacheDir() . 'container.php';
        $cache = new ConfigCache($file, ($this->appMode === ApplicationMode::DEVELOPMENT));

        $this->dumpContainer($cache);

        require_once $cache->getPath();

        $this->container = new $this->containerName();
        $this->container->set('core.environment.application_path', $this->appPath);
        $this->container->set('kernel', $this);
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function dumpContainer(ConfigCache $cache): void
    {
        if (!$cache->isFresh()) {
            $containerBuilder = ServiceContainerBuilder::create(
                $this->appPath
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
        } finally {
            /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
            $requestStack = $this->container->get('request_stack');
            $requestStack->pop();
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
