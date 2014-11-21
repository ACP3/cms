<?php
namespace ACP3\Modules\Minify;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\Minify
 */
class Cache
{
    const CACHE_ID = 'assets';
    /**
     * @var \ACP3\Core\Cache
     */
    protected $cache;

    /**
     * @param Core\Cache $cache
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
     * @return bool
     */
    public function setCache(array $paths)
    {
        return $this->cache->save(self::CACHE_ID, $paths);
    }

}