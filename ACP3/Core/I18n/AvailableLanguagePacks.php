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
     * @var ApplicationPath
     */
    private $appPath;
    /**
     * @var LanguagePacksCollector
     */
    private $languagePacksCollector;
    /**
     * @var array
     */
    private $languagePacks = [];

    /**
     * AvailableLanguagePacks constructor.
     * @param ApplicationPath $appPath
     * @param LanguagePacksCollector $languagePacksCollector
     */
    public function __construct(
        ApplicationPath $appPath,
        LanguagePacksCollector $languagePacksCollector
    ) {
        $this->appPath = $appPath;
        $this->languagePacksCollector = $languagePacksCollector;
    }

    /**
     * Checks, whether the given locale exists as a language pack
     *
     * @param string $locale
     *
     * @return boolean
     */
    public function languagePackExists(string $locale): bool
    {
        return !\preg_match('=/=', $locale)
            && \is_file($this->appPath->getModulesDir() . 'ACP3/System/Resources/i18n/' . $locale . '.xml') === true;
    }

    /**
     * Gets all currently available languages
     *
     * @param string $currentLocale
     * @return array
     */
    public function getAvailableLanguagePacks(string $currentLocale): array
    {
        if (empty($this->languagePacks)) {
            $this->languagePacks = $this->languagePacksCollector->getLanguagePacksCache();
        }

        $languages = $this->languagePacks;
        foreach ($languages as $key => $value) {
            $languages[$key]['selected'] = $languages[$key]['iso'] === $currentLocale;
        }

        return $languages;
    }
}
