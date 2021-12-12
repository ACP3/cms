<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class is responsible for caching the enabled libraries on a per request bases. This includes sub requests too!
 */
class LibrariesCache
{
    /**
     * @var array<string, string[]>
     */
    private $librariesCache = [];

    public function __construct(private CacheItemPoolInterface $librariesCachePool)
    {
    }

    public function scheduleStoreEnabledLibraryInCache(Request $request, string $library): void
    {
        if (!\array_key_exists($request->getUri(), $this->librariesCache)) {
            $this->librariesCache[$request->getUri()] = [];
        }

        $this->librariesCache[$request->getUri()][] = $library;
    }

    public function hasCacheForRequest(Request $request): bool
    {
        return $this->librariesCachePool->hasItem($this->getCacheId($request));
    }

    /**
     * @return string[]
     */
    public function getEnabledLibrariesByRequest(Request $request): array
    {
        if (!$this->hasCacheForRequest($request)) {
            return $this->librariesCache[$request->getUri()] ?? [];
        }

        return $this->librariesCachePool->getItem($this->getCacheId($request))->get();
    }

    /**
     * Conditionally saves the enabled frontend libraries into a cache file.
     * If the cache file already exists, we do nothing here.
     *
     * Background: When certain controller actions are already stored within the HTTP cache, these don't get called and
     * therefore the method calls to ACP3\Core\Assets\Libraries::enabledLibraries() don't get called either.
     */
    public function saveEnabledLibrariesByRequest(Request $request): void
    {
        if ($this->hasCacheForRequest($request)) {
            return;
        }

        $cacheItem = $this->librariesCachePool->getItem($this->getCacheId($request));
        $cacheItem->set(array_unique($this->librariesCache[$request->getUri()] ?? []));
        $this->librariesCachePool->saveDeferred($cacheItem);
    }

    private function getCacheId(Request $request): string
    {
        return 'libraries_' . md5($request->getUri());
    }

    public function deleteAll(): bool
    {
        return $this->librariesCachePool->clear();
    }
}
