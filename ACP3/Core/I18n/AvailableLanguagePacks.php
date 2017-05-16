<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;


use ACP3\Core\Environment\ApplicationPath;

class AvailableLanguagePacks
{
    /**
     * @var DictionaryCache
     */
    private $dictionaryCache;

    /**
     * @var array
     */
    private $languagePacks = [];
    /**
     * @var ApplicationPath
     */
    private $appPath;

    /**
     * AvailableLanguagePacks constructor.
     * @param ApplicationPath $appPath
     * @param DictionaryCache $dictionaryCache
     */
    public function __construct(ApplicationPath $appPath, DictionaryCache $dictionaryCache)
    {
        $this->dictionaryCache = $dictionaryCache;
        $this->appPath = $appPath;
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert
     *
     * @param string $locale
     *
     * @return boolean
     */
    public function languagePackExists(string $locale): bool
    {
        return !preg_match('=/=', $locale)
            && is_file($this->appPath->getModulesDir() . 'ACP3/System/Resources/i18n/' . $locale . '.xml') === true;
    }

    /**
     * Gets all currently available languages
     *
     * @param string $currentLanguage
     *
     * @return array
     */
    public function getLanguagePacks(string $currentLanguage): array
    {
        if (empty($this->languagePacks)) {
            $this->languagePacks = $this->dictionaryCache->getLanguagePacksCache();
        }

        $languages = $this->languagePacks;
        foreach ($languages as $key => $value) {
            $languages[$key]['selected'] = $languages[$key]['iso'] === $currentLanguage;
        }

        return $languages;
    }
}
