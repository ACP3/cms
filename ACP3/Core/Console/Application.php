<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Console;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Console\DependencyInjection\ServiceContainerBuilder;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\ErrorHandler;
use ACP3\Core\Logger\LoggerFactory;
use Patchwork\Utf8\Bootup;
use Psr\Container\ContainerInterface;

class Application
{
    /**
     * @var string
     */
    private $environment;
    /**
     * @var ApplicationPath
     */
    private $appPath;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Application constructor.
     *
     * @param string $environment
     */
    public function __construct(string $environment)
    {
        $this->environment = $environment;

        $this->initializeApplicationPath();
        $this->logger = (new LoggerFactory($this->appPath))->create('console');
    }

    private function initializeApplicationPath()
    {
        $this->appPath = new ApplicationPath($this->environment);
    }

    public function run(): int
    {
        $this->setErrorHandler();
        $this->initializeClasses();

        /** @var \Symfony\Component\Console\Application $console */
        $console = $this->container->get('symfony_console');

        $console->setName('ACP3 CMS console application');
        $console->setVersion(BootstrapInterface::VERSION);

        return $console->run();
    }

    /**
     * Set monolog as the default PHP error handler.
     */
    private function setErrorHandler()
    {
        ErrorHandler::register($this->logger);
    }

    public function initializeClasses()
    {
        Bootup::initAll(); // Enables the portability layer and configures PHP for UTF-8
        Bootup::filterRequestUri(); // Redirects to an UTF-8 encoded URL if it's not already the case
        Bootup::filterRequestInputs(); // Normalizes HTTP inputs to UTF-8 NFC

        $this->container = ServiceContainerBuilder::create(
            $this->logger,
            $this->appPath,
            $this->environment
        );
    }
}
