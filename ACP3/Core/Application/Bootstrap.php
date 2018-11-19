<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Exception\MaintenanceModeActiveException;
use ACP3\Core\Controller\Exception\ForwardControllerActionAwareExceptionInterface;
use ACP3\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Core\Environment\ApplicationMode;
use Patchwork\Utf8;
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
     */
    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->setErrorHandler();
        $this->initializeClasses($request);

        return $this->outputPage();
    }

    /**
     * {@inheritdoc}
     */
    public function initializeClasses(SymfonyRequest $symfonyRequest)
    {
        Utf8\Bootup::initAll(); // Enables the portability layer and configures PHP for UTF-8
        Utf8\Bootup::filterRequestUri(); // Redirects to an UTF-8 encoded URL if it's not already the case
        Utf8\Bootup::filterRequestInputs(); // Normalizes HTTP inputs to UTF-8 NFC

        $file = $this->appPath->getCacheDir() . 'container.php';
        $cache = new ConfigCache($file, ($this->appMode === ApplicationMode::DEVELOPMENT));

        $this->dumpContainer($symfonyRequest, $cache);

        require_once $cache->getPath();

        $this->container = new $this->containerName();
        $this->container->set('core.environment.application_path', $this->appPath);
        $this->container->set('core.http.symfony_request', $symfonyRequest);
    }

    /**
     * @param SymfonyRequest                        $symfonyRequest
     * @param \Symfony\Component\Config\ConfigCache $cache
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function dumpContainer(SymfonyRequest $symfonyRequest, ConfigCache $cache)
    {
        if (!$cache->isFresh()) {
            $containerBuilder = ServiceContainerBuilder::create(
                $this->appPath, $symfonyRequest, $this->appMode
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
     * @throws \Exception
     */
    public function outputPage()
    {
        /** @var \ACP3\Core\Application\ControllerActionDispatcher $controllerActionDispatcher */
        $controllerActionDispatcher = $this->container->get('core.application.controller_action_dispatcher');

        try {
            /** @var \ACP3\Core\Authentication\AuthenticationInterface $authentication */
            $authentication = $this->container->get('core.authentication');
            $authentication->authenticate();

            /** @var \ACP3\Core\Application\ControllerActionDispatcher $controllerActionDispatcher */
            $controllerActionDispatcher = $this->container->get('core.application.controller_action_dispatcher');

            $response = $controllerActionDispatcher->dispatch();
        } catch (ForwardControllerActionAwareExceptionInterface $e) {
            $response = $controllerActionDispatcher->dispatch($e->getServiceId(), $e->routeParams());
        } catch (MaintenanceModeActiveException $e) {
            $response = new Response($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            $this->logger->critical($e);

            throw $e;
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function startupChecks()
    {
        \date_default_timezone_set('UTC');

        return $this->databaseConfigExists();
    }
}
