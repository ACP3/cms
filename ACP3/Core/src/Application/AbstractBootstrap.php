<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

abstract class AbstractBootstrap implements BootstrapInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var string
     */
    protected $appMode;
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
    public function __construct(string $appMode)
    {
        $this->appMode = $appMode;
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

            if ($this->container->has('http_cache')) {
                return $this->container->get('http_cache')->handle($request, $type, $catch);
            }
        }

        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');
        $requestStack->push($request);

        return $this->outputPage();
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
     * Checks, whether the database configuration file exists.
     */
    protected function databaseConfigExists(): bool
    {
        $path = $this->appPath->getAppDir() . 'config.yml';

        return \is_file($path) === true && \filesize($path) !== 0;
    }
}
