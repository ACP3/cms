<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use Psr\Cache\CacheItemPoolInterface;

class CachingModuleInfo implements ModuleInfoInterface
{
    private const CACHE_ID_MODULES_INFO = 'modules_info';

    public function __construct(private CacheItemPoolInterface $coreCachePool, private ModuleInfo $moduleInfo)
    {
    }

    public function getModulesInfo(): array
    {
        $cacheItem = $this->coreCachePool->getItem(self::CACHE_ID_MODULES_INFO);

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->moduleInfo->getModulesInfo());
            $this->coreCachePool->saveDeferred($cacheItem);
        }

        return $cacheItem->get();
    }
}
