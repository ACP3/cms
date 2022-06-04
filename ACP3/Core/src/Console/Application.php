<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Console\DependencyInjection\ServiceContainerBuilder;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\ErrorHandler;

class Application
{
    private ?ApplicationPath $appPath = null;

    private ?ContainerInterface $container = null;

    private ?LoggerInterface $logger;

    /**
     * @throws \Exception
     */
    public function __construct(private readonly ApplicationMode $environment)
    {
        $this->initializeApplicationPath();
        $this->logger = (new LoggerFactory($this->appPath))->create('console');
    }

    private function initializeApplicationPath(): void
    {
        $this->appPath = new ApplicationPath($this->environment);
    }

    public function run(): int
    {
        $this->setErrorHandler();
        $this->initializeClasses();

        /** @var \Symfony\Component\Console\Application $console */
        $console = $this->container->get(\Symfony\Component\Console\Application::class);

        $console->setName('ACP3 CMS console application');
        $console->setVersion(BootstrapInterface::VERSION);

        return $console->run();
    }

    /**
     * Set monolog as the default PHP error handler.
     */
    private function setErrorHandler(): void
    {
        $errorHandler = new ErrorHandler();
        $errorHandler->setDefaultLogger($this->logger);
        ErrorHandler::register($errorHandler);
    }

    public function initializeClasses(): void
    {
        $this->container = ServiceContainerBuilder::create(
            $this->logger, $this->appPath
        );
    }
}
