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

/**
 * Class Cache
 * @package ACP3\Core\I18n
 */
class DictionaryCache implements DictionaryCacheInterface
{
    use ExtractFromPathTrait;

    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    protected $vendors;

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
     * @inheritdoc
     */
    public function getLanguageCache(string $locale): array
    {
        if ($this->cache->contains($locale) === false) {
            $this->saveLanguageCache($locale);
        }

        return $this->cache->fetch($locale);
    }

    /**
     * Saves the language cache
     *
     * @param string $locale
     *
     * @return bool
     */
    public function saveLanguageCache(string $locale): bool
    {
        $data = [];

        foreach ($this->vendors->getVendors() as $vendor) {
            $languageFiles = glob($this->appPath->getModulesDir() . $vendor . '/*/Resources/i18n/' . $locale . '.xml');

            if ($languageFiles !== false) {
                foreach ($languageFiles as $file) {
                    if (isset($data['info']['direction']) === false) {
                        $localeInfo = Locale::create($locale);
                        $data['info']['direction'] = $localeInfo->script()->direction();
                    }

                    $module = $this->getModuleFromPath($file);

                    // Iterate over all language keys
                    $xml = simplexml_load_file($file);
                    foreach ($xml->keys->item as $item) {
                        $data['keys'][strtolower($module . (string)$item['key'])] = trim((string)$item);
                    }
                }
            }
        }

        return $this->cache->save($locale, $data);
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
