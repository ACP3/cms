<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Services;

use Psr\Cache\CacheItemPoolInterface;

class CachingEmoticonService implements EmoticonServiceInterface
{
    private const CACHE_KEY = 'emoticon_list';

    public function __construct(private readonly CacheItemPoolInterface $emoticonsCachePool, private readonly EmoticonService $emoticonService)
    {
    }

    public function getEmoticonList(): array
    {
        $cacheItem = $this->emoticonsCachePool->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->emoticonService->getEmoticonList());
            $this->emoticonsCachePool->saveDeferred($cacheItem);
        }

        return $cacheItem->get();
    }
}
