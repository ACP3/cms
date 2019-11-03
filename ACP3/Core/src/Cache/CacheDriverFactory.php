<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\PhpFileCache;

class CacheDriverFactory
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var string
     */
    private $cacheDriver;
    /**
     * @var string
     */
    private $environment;

    /**
     * CacheDriverFactory constructor.
     */
    public function __construct(ApplicationPath $appPath, string $cacheDriver, string $environment)
    {
        $this->appPath = $appPath;
        $this->cacheDriver = $cacheDriver;
        $this->environment = $environment;
    }

    public function create(string $namespace): CacheProvider
    {
        $driver = $this->initializeCacheDriver($this->getCacheDriverName());
        $driver->setNamespace($namespace);

        return $driver;
    }

    protected function getCacheDriverName(): string
    {
        return $this->environment !== ApplicationMode::DEVELOPMENT ? $this->cacheDriver : 'Array';
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function initializeCacheDriver(string $driverName): CacheProvider
    {
        /* @var \Doctrine\Common\Cache\CacheProvider $driver */
        switch (\strtolower($driverName)) {
            case 'phpfile':
                return new PhpFileCache($this->appPath->getCacheDir() . 'sql/');
            case 'array':
                return new ArrayCache();
            default:
                throw new \InvalidArgumentException(\sprintf('Could not find the requested cache driver "%s"!', $driverName));
        }
    }
}
