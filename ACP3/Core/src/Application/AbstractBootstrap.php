<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Symfony\Component\ErrorHandler\ErrorHandler;

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
     * Set monolog as the default PHP error handler.
     */
    public function setErrorHandler(): void
    {
        $errorHandler = new ErrorHandler();

        if ($this->appMode === ApplicationMode::PRODUCTION) {
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
