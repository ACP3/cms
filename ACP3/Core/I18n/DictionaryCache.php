<?php
namespace ACP3\Core\I18n;

use ACP3\Core\Cache;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules\Vendors;

/**
 * Class Cache
 * @package ACP3\Core\I18n
 */
class DictionaryCache
{
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Modules\Vendors
     */
    protected $vendors;

    /**
     * DictionaryCache constructor.
     *
     * @param \ACP3\Core\Cache                       $cache
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Modules\Vendors             $vendors
     */
    public function __construct(
        Cache $cache,
        ApplicationPath $appPath,
        Vendors $vendors
    )
    {
        $this->cache = $cache;
        $this->appPath = $appPath;
        $this->vendors = $vendors;
    }

    /**
     * Returns the cached language strings
     *
     * @param string $language
     *
     * @return array
     */
    public function getLanguageCache($language)
    {
        if ($this->cache->contains($language) === false) {
            $this->saveLanguageCache($language);
        }

        return $this->cache->fetch($language);
    }

    /**
     * Saves the language cache
     *
     * @param string $language
     *
     * @return bool
     */
    public function saveLanguageCache($language)
    {
        $data = [];

        foreach ($this->vendors->getVendors() as $vendor) {
            $languageFiles = glob($this->appPath->getModulesDir() . $vendor . '/*/Resources/i18n/' . $language . '.xml');

            if ($languageFiles !== false) {
                foreach ($languageFiles as $file) {
                    $xml = simplexml_load_file($file);
                    if (isset($data['info']['direction']) === false) {
                        $data['info']['direction'] = (string)$xml->info->direction;
                    }

                    $module = $this->getModuleFromPath($file);

                    // Iterate over all language keys
                    foreach ($xml->keys->item as $item) {
                        $data['keys'][strtolower($module . (string)$item['key'])] = trim((string)$item);
                    }
                }
            }
        }

        return $this->cache->save($language, $data);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getModuleFromPath($path)
    {
        $pathArray = explode('/', $path);

        return $pathArray[count($pathArray) - 4];
    }

    /**
     * Gets the cache for all registered languages
     *
     * @return array
     */
    public function getLanguagePacksCache()
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
    protected function saveLanguagePacksCache()
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
     * @param $file
     *
     * @return array
     */
    protected function registerLanguagePack($file)
    {
        $xml = simplexml_load_file($file);

        if (!empty($xml)) {
            $languageIso = $this->getLanguagePackIsoCode($file);

            return [
                $languageIso => [
                    'iso' => $languageIso,
                    'name' => (string)$xml->info->name
                ]
            ];
        }

        return [];
    }

    /**
     * @param $file
     *
     * @return string
     */
    protected function getLanguagePackIsoCode($file)
    {
        return substr($file, strrpos($file, '/') + 1, -4);
    }

}