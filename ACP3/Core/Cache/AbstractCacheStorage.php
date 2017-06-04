<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core;

abstract class AbstractCacheStorage
{
    /**
     * @var \ACP3\Core\Cache\Cache
     */
    protected $cache;

    /**
     * @param \ACP3\Core\Cache\Cache $cache
     */
    public function __construct(Core\Cache\Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getCacheDriver()
    {
        return $this->cache->getDriver();
    }
}
