<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\ErrorHandler;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param string $appMode
     */
    public function __construct(string $appMode)
    {
        $this->appMode = $appMode;
        $this->initializeApplicationPath();
        $this->logger = (new \ACP3\Core\Logger\LoggerFactory($this->appPath))->create('system');
    }

    protected function initializeApplicationPath()
    {
        $this->appPath = new ApplicationPath($this->appMode);
    }

    /**
     * Set monolog as the default PHP error handler.
     */
    public function setErrorHandler()
    {
        ErrorHandler::register($this->logger);
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return ApplicationPath
     */
    public function getAppPath()
    {
        return $this->appPath;
    }

    /**
     * Checks, whether the database configuration file exists.
     *
     * @return bool
     */
    protected function databaseConfigExists()
    {
        $path = $this->appPath->getAppDir() . 'config.yml';

        return \is_file($path) === true && \filesize($path) !== 0;
    }
}
