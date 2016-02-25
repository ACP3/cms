<?php
namespace ACP3\Core\Application;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\ErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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
    public function __construct($appMode = ApplicationMode::PRODUCTION)
    {
        $this->appMode = $appMode;
        $this->setAppPath($appMode);
    }

    /**
     * @param string $appMode
     */
    protected function setAppPath($appMode)
    {
        $this->appPath = new ApplicationPath($appMode);
    }

    /**
     * Set monolog as the default PHP error handler
     */
    public function setErrorHandler()
    {
        $stream = new StreamHandler($this->appPath->getCacheDir() . 'logs/system.log', Logger::NOTICE);
        $stream->setFormatter(new LineFormatter(null, null, true));

        $logger = new Logger('system', [$stream]);
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
        if (is_file($path) === false || filesize($path) === 0) {
            echo 'The ACP3 is not correctly installed. Please navigate to the <a href="' . $this->appPath->getWebRoot() . 'installation/">installation wizard</a> and follow its instructions.';
            return false;
        }

        return true;
    }
}
