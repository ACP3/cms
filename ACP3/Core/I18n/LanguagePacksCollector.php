<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;

use ACP3\Core\Cache;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules\Vendor;
use Fisharebest\Localization\Locale;

class LanguagePacksCollector
{
    use ExtractFromPathTrait;

    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    private $vendors;

    /**
     * DictionaryCache constructor.
     *
     * @param \ACP3\Core\Cache $cache
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Modules\Vendor $vendors
     */
    public function __construct(
        Cache $cache,
        ApplicationPath $appPath,
        Vendor $vendors
    ) {
        $this->cache = $cache;
        $this->appPath = $appPath;
        $this->vendors = $vendors;
    }

    /**
     * Gets the cache for all registered languages
     *
     * @return array
     */
    public function getLanguagePacksCache(): array
    {
        if ($this->cache->contains('language_packs') === false) {
            $this->saveLanguagePacksCache();
        }

        return $this->cache->fetch('language_packs');
    }

    /**
     * Sets the cache for all registered languages
     *
     * @return bool
     */
    private function saveLanguagePacksCache(): bool
    {
        $languagePacks = [];

        foreach ($this->vendors->getVendors() as $vendors) {
            $languageFiles = glob($this->appPath->getModulesDir() . $vendors . '/*/Resources/i18n/*.xml');

            if ($languageFiles !== false) {
                foreach ($languageFiles as $file) {
                    $languagePack = $this->registerLanguagePack($file);

                    if (!empty($languagePack)) {
                        $languagePacks += $languagePack;
                    }
                }
            }
        }

        return $this->cache->save('language_packs', $languagePacks);
    }

    /**
     * @param string $file
     *
     * @return array
     */
    private function registerLanguagePack(string $file): array
    {
        $languageIso = $this->getLanguagePackIsoCode($file);

        try {
            $locale = Locale::create($languageIso);

            return [
                $languageIso => [
                    'iso' => $languageIso,
                    'name' => $locale->endonym()
                ]
            ];
        } catch (\DomainException $e) {
            return [];
        }
    }
}
