<?php
namespace ACP3\Core\Modules;

use ACP3\Core;

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
