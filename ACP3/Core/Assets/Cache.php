<?php
namespace ACP3\Core\Assets;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Core\Assets
 */
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
    public function setCache(array $paths)
    {
        return $this->cache->save(self::CACHE_ID, $paths);
    }
}
