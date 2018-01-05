<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core;

class Cache
{
    const CACHE_ID = 'resources';
    /**
     * @var \ACP3\Core\Cache
     */
    protected $cache;

    /**
     * @param \ACP3\Core\Cache $cache
     */
    public function __construct(Core\Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return array
     */
    public function getCache()
    {
        if ($this->cache->contains(self::CACHE_ID) === true) {
            return $this->cache->fetch(self::CACHE_ID);
        }

        return [];
    }

    /**
     * @param array $paths
     *
     * @return bool
     */
    public function saveCache(array $paths)
    {
        return $this->cache->save(self::CACHE_ID, $paths);
    }
}
