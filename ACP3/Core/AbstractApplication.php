<?php
namespace ACP3\Core;

/**
 * Class AbstractApplication
 * @package ACP3\Core
 */
abstract class AbstractApplication implements ApplicationInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @inheritdoc
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Checks, whether the database configuration file exists
     */
    protected function checkForDbConfig()
    {
        $path = ACP3_DIR . 'config.yml';
        if (is_file($path) === false || filesize($path) === 0) {
            exit('The ACP3 is not correctly installed. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow its instructions.');
        }
    }

}