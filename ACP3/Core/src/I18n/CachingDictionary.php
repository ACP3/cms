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

    /**
     * @var CacheItemPoolInterface
     */
    private $i18nCachePool;
    /**
     * @var Dictionary
     */
    private $dictionary;

    public function __construct(CacheItemPoolInterface $i18nCachePool, Dictionary $dictionary)
    {
        $this->i18nCachePool = $i18nCachePool;
        $this->dictionary = $dictionary;
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
