<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Cache;


use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CacheDriverFactory
 * @package ACP3\Core\Cache
 */
class CacheDriverFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * CacheDriverFactory constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \ACP3\Core\Environment\ApplicationPath                    $appPath
     */
    public function __construct(ContainerInterface $container, ApplicationPath $appPath)
    {
        $this->container = $container;
        $this->appPath = $appPath;
    }

    /**
     * @param string $namespace
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function create($namespace)
    {
        $driverName = $this->getCacheDriverName();

        $cacheDriverPath = "\\Doctrine\\Common\\Cache\\" . $driverName . 'Cache';
        if (class_exists($cacheDriverPath)) {
            $driver = $this->initializeCacheDriver($driverName, $cacheDriverPath);
            $driver->setNamespace($namespace);

            return $driver;
        }

        throw new \InvalidArgumentException(
            sprintf('Could not find the requested cache driver "%s"!', $cacheDriverPath)
        );
    }

    /**
     * @return mixed|string
     */
    protected function getCacheDriverName()
    {
        return $this->containerHasCacheDriver() ? $this->container->getParameter('cache_driver') : 'Array';
    }

    /**
     * @return bool
     */
    protected function containerHasCacheDriver()
    {
        return $this->container->hasParameter('cache_driver')
        && $this->container->getParameter('core.environment') !== ApplicationMode::DEVELOPMENT;
    }

    /**
     * @param string $driverName
     * @param string $driverNameFqdn
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    protected function initializeCacheDriver($driverName, $driverNameFqdn)
    {
        /** @var \Doctrine\Common\Cache\CacheProvider $driver */
        switch (strtolower($driverName)) {
            case 'phpfile':
                $cacheDir = $this->appPath->getCacheDir() . 'sql/';
                return new $driverNameFqdn($cacheDir);
            default:
                return new $driverNameFqdn();
        }
    }
}
