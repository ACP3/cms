<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

interface DictionaryCacheInterface
{
    /**
     * Returns the cached language strings.
     */
    public function getLanguageCache(string $language): array;

    /**
     * Saves the language cache.
     */
    public function saveLanguageCache(string $language): bool;

    /**
     * Gets the cache for all registered languages.
     */
    public function getLanguagePacksCache(): array;
}
