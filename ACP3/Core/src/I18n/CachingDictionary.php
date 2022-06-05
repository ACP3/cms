<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

use Psr\Cache\CacheItemPoolInterface;

class CachingDictionary implements DictionaryInterface
{
    private const CACHE_ID_LANG_PACKS = 'language_packs';

    public function __construct(private readonly CacheItemPoolInterface $i18nCachePool, private readonly Dictionary $dictionary)
    {
    }

    public function getDictionary(string $language): array
    {
        $cacheItem = $this->i18nCachePool->getItem($language);

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->dictionary->getDictionary($language));
            $this->i18nCachePool->saveDeferred($cacheItem);
        }

        return $cacheItem->get();
    }

    public function getLanguagePacks(): array
    {
        $cacheItem = $this->i18nCachePool->getItem(self::CACHE_ID_LANG_PACKS);

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->dictionary->getLanguagePacks());
            $this->i18nCachePool->saveDeferred($cacheItem);
        }

        return $cacheItem->get();
    }
}
