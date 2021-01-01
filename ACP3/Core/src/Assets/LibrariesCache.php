<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Cache;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class is responsible for caching the enabled libraries on a per request bases. This includes sub requests too!
 */
class LibrariesCache
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Array<string, string[]>
     */
    private $librariesCache = [];

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
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
        return $this->cache->contains($this->getCacheId($request));
    }

    /**
     * @return string[]
     */
    public function getEnabledLibrariesByRequest(Request $request): array
    {
        if (!$this->hasCacheForRequest($request)) {
            return $this->librariesCache[$request->getUri()] ?? [];
        }

        return $this->cache->fetch($this->getCacheId($request));
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

        $this->cache->save($this->getCacheId($request), \array_unique($this->librariesCache[$request->getUri()] ?? []));
    }

    private function getCacheId(Request $request): string
    {
        return 'libraries_' . \md5($request->getUri());
    }

    public function deleteAll(): bool
    {
        return $this->cache->deleteAll();
    }
}
