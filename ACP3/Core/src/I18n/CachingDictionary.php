<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

use ACP3\Core\Cache;

class CachingDictionary implements DictionaryInterface
{
    private const CACHE_ID_LANG_PACKS = 'language_packs';

    /**
     * @var Cache
     */
    private $i18nCache;
    /**
     * @var Dictionary
     */
    private $dictionary;

    public function __construct(Cache $i18nCache, Dictionary $dictionary)
    {
        $this->i18nCache = $i18nCache;
        $this->dictionary = $dictionary;
    }

    public function getDictionary(string $language): array
    {
        if (!$this->i18nCache->contains($language)) {
            $this->i18nCache->save($language, $this->dictionary->getDictionary($language));
        }

        return $this->i18nCache->fetch($language);
    }

    public function getLanguagePacks(): array
    {
        if (!$this->i18nCache->contains(self::CACHE_ID_LANG_PACKS)) {
            $this->i18nCache->save(self::CACHE_ID_LANG_PACKS, $this->dictionary->getLanguagePacks());
        }

        return $this->i18nCache->fetch(self::CACHE_ID_LANG_PACKS);
    }
}
