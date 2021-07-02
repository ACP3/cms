<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Cache\CacheDriverFactory;
use Doctrine\Common\Cache\CacheProvider;

class Cache
{
    /**
     * @var \ACP3\Core\Cache\CacheDriverFactory
     */
    private $cacheDriverFactory;
    /**
     * @var string
     */
    private $namespace;
    /**
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    private $driver;

    public function __construct(CacheDriverFactory $cacheDriverFactory, string $namespace)
    {
        $this->cacheDriverFactory = $cacheDriverFactory;
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function fetch(string $cacheId)
    {
        return $this->getDriver()->fetch($cacheId);
    }

    public function contains(string $cacheId): bool
    {
        return $this->getDriver()->contains($cacheId);
    }

    /**
     * @param mixed $data
     */
    public function save(string $cacheId, $data, int $lifetime = 0): bool
    {
        return $this->getDriver()->save($cacheId, $data, $lifetime);
    }

    public function delete(string $cacheId): bool
    {
        return $this->getDriver()->delete($cacheId);
    }

    public function deleteAll(): bool
    {
        return $this->getDriver()->deleteAll();
    }

    public function flushAll(): bool
    {
        return $this->getDriver()->flushAll();
    }

    public function getDriver(): CacheProvider
    {
        if ($this->driver === null) {
            $this->driver = $this->cacheDriverFactory->create($this->namespace);
        }

        return $this->driver;
    }
}
