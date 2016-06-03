<?php
namespace ACP3\Core\Application;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\ErrorHandler;

/**
 * Class AbstractBootstrap
 * @package ACP3\Core\Application
 */
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
     * @param string $appMode
     */
    public function __construct($appMode)
    {
        $this->appMode = $appMode;
        $this->initializeApplicationPath($this->appMode);
    }

    /**
     * @param string $appMode
     */
    protected function initializeApplicationPath($appMode)
    {
        $this->appPath = new ApplicationPath($appMode);
    }

    /**
     * Set monolog as the default PHP error handler
     */
    public function setErrorHandler()
    {
        $logger = new \ACP3\Core\Logger($this->appPath);

        ErrorHandler::register($logger);
    }

    /**
     * @inheritdoc
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Checks, whether the database configuration file exists
     *
     * @return bool
     */
    protected function databaseConfigExists()
    {
        $path = $this->appPath->getAppDir() . 'config.yml';

        return is_file($path) === true && filesize($path) !== 0;
    }
}
