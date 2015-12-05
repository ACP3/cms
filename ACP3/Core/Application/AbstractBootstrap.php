<?php
namespace ACP3\Core\Application;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Enum\Environment;
use ACP3\Core\ErrorHandler;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

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
    protected $environment;

    /**
     * @param string $environment
     */
    public function __construct($environment = Environment::PRODUCTION)
    {
        $this->environment = $environment;
    }

    /**
     * Set monolog as the default PHP error handler
     */
    public function setErrorHandler()
    {
        $stream = new StreamHandler(CACHE_DIR . 'logs/system.log', Logger::NOTICE);
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
        $path = ACP3_DIR . 'config.yml';
        if (is_file($path) === false || filesize($path) === 0) {
            echo 'The ACP3 is not correctly installed. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow its instructions.';
            return false;
        }

        return true;
    }

}