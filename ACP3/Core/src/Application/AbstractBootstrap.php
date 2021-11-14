<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Event\OutputPageExceptionEvent;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\TerminableInterface;

abstract class AbstractBootstrap implements BootstrapInterface, TerminableInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * @var bool
     */
    private $booted = false;

    /**
     * @throws \Exception
     */
    public function __construct(protected string $appMode)
    {
        $this->initializeApplicationPath();
    }

    protected function initializeApplicationPath()
    {
        $this->appPath = new ApplicationPath($this->appMode);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Throwable
     */
    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        if (!$this->booted) {
            $this->boot();

            if ($this->container->has(BootstrapCache::class)) {
                return $this->container->get(BootstrapCache::class)->handle($request, $type, $catch);
            }
        }

        return $this->outputPage($request, $catch);
    }

    private function boot(): void
    {
        if (true === $this->booted) {
            return;
        }

        if (null === $this->container) {
            $this->preBoot();
        }

        $this->booted = true;
    }

    private function preBoot(): void
    {
        $this->setErrorHandler();
        $this->initializeClasses();
    }

    /**
     * Set monolog as the default PHP error handler.
     */
    public function setErrorHandler(): void
    {
        $isProduction = $this->appMode === ApplicationMode::PRODUCTION;

        $errorHandler = new ErrorHandler(null, !$isProduction);

        if ($isProduction) {
            $errorHandler->scopeAt(0, true);
        }

        ErrorHandler::register($errorHandler);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Throwable
     */
    public function outputPage(Request $request, bool $catch): Response
    {
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $requestStack = $this->container->get(RequestStack::class);

        $requestStack->push($request);

        /** @var \ACP3\Core\Application\ControllerActionDispatcher $controllerActionDispatcher */
        $controllerActionDispatcher = $this->container->get(ControllerActionDispatcher::class);

        try {
            return $controllerActionDispatcher->dispatch();
        } catch (\Throwable $e) {
            if ($catch && $response = $this->handleException($e)) {
                return $response;
            }

            throw $e;
        } finally {
            $requestStack->pop();
        }
    }

    private function handleException(\Throwable $exception): ?Response
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('core.event_dispatcher');

        $outputPageExceptionEvent = new OutputPageExceptionEvent($exception);
        $eventDispatcher->dispatch($outputPageExceptionEvent, OutputPageExceptionEvent::NAME);

        if ($outputPageExceptionEvent->hasResponse()) {
            return $outputPageExceptionEvent->getResponse();
        }

        return null;
    }

    /**
     * Checks, whether the database configuration file exists.
     */
    protected function databaseConfigExists(): bool
    {
        $path = $this->appPath->getAppDir() . 'config.yml';

        return is_file($path) === true && filesize($path) !== 0;
    }

    public function terminate(SymfonyRequest $request, Response $response)
    {
        if (!$this->booted) {
            return;
        }

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('core.event_dispatcher');

        $eventDispatcher->dispatch(new TerminateEvent($this, $request, $response), KernelEvents::TERMINATE);
    }
}
