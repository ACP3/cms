<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core;

/**
 * @deprecated since Version 5.19.0, to be removed with version 6.0.0.
 */
class AbstractCacheStorage
{
    /**
     * @var \ACP3\Core\Cache
     */
    protected $cache;

    /**
     * @param \ACP3\Core\Cache $cache
     */
    protected function __construct(Core\Cache $cache)
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
