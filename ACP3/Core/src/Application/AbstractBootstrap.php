<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Application\Event\OutputPageExceptionEvent;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Composer\InstalledVersions;
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
    private static ?string $version = null;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var ApplicationPath
     */
    protected $appPath;

    private bool $booted = false;

    /**
     * @throws \Exception
     */
    public function __construct(protected ApplicationMode $appMode)
    {
        $this->initializeApplicationPath();
    }

    protected function initializeApplicationPath(): void
    {
        $this->appPath = new ApplicationPath($this->appMode);
    }

    /**
     * @throws \Throwable
     */
    public function handle(SymfonyRequest $request, $type = self::MAIN_REQUEST, $catch = true): Response
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
     * @throws \Throwable
     */
    public function outputPage(Request $request, bool $catch): Response
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->container->get(RequestStack::class);

        $requestStack->push($request);

        /** @var ControllerActionDispatcher $controllerActionDispatcher */
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
        $eventDispatcher = $this->container->get('event_dispatcher');

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

    public function terminate(SymfonyRequest $request, Response $response): void
    {
        if (!$this->booted) {
            return;
        }

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');

        $eventDispatcher->dispatch(new TerminateEvent($this, $request, $response), KernelEvents::TERMINATE);
    }

    public static function getVersion(): string
    {
        if (!self::$version) {
            $corePackageComposerJson = json_decode(
                file_get_contents(\dirname(__DIR__, 2) . '/composer.json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
            self::$version = InstalledVersions::getPrettyVersion($corePackageComposerJson['name']) ?: '99.9.9-dev';
        }

        return self::$version;
    }
}
