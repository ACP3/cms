<?php
namespace ACP3\Core\Lang;
use ACP3\Core\Modules\Vendors;

/**
 * Class Cache
 * @package ACP3\Core\Lang
 */
class Cache
{
    /**
     * @var \ACP3\Core\Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Core\Modules\Vendors
     */
    protected $vendors;

    /**
     * @param \ACP3\Core\Cache           $cache
     * @param \ACP3\Core\Modules\Vendors $vendors
     */
    public function __construct(
        \ACP3\Core\Cache $cache,
        Vendors $vendors
    )
    {
        $this->cache = $cache;
        $this->vendors = $vendors;
    }

    /**
     * Gibt die gecacheten Sprachstrings aus
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
     * Cacht die Sprachfiles, um diese schneller verarbeiten zu können
     *
     * @param string $language
     *
     * @return bool
     */
    public function saveLanguageCache($language)
    {
        $data = [];

        foreach ($this->vendors->getVendors() as $namespace) {
            $languageFiles = glob(MODULES_DIR . $namespace . '/*/Languages/' . $language . '.xml');

            if ($languageFiles !== false) {
                foreach ($languageFiles as $file) {
                    $xml = simplexml_load_file($file);
                    if (isset($data['info']['direction']) === false) {
                        $data['info']['direction'] = (string)$xml->info->direction;
                    }

                    $module = $this->getModuleFromPath($file);

                    // Iterate over all language keys
                    foreach ($xml->keys->item as $item) {
                        $data['keys'][strtolower($module)][(string)$item['key']] = trim((string)$item);
                    }
                }
            }
        }

        return $this->cache->save($language, $data);
    }

    /**
     * @param $path
     *
     * @return string
     */
    protected function getModuleFromPath($path)
    {
        $pathArray = explode('/', $path);

        return $pathArray[count($pathArray) - 3];
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

        foreach ($this->vendors->getVendors() as $namespace) {
            $languageFiles = glob(MODULES_DIR . $namespace . '/*/Languages/*.xml');

            if ($languageFiles !== false) {
                foreach ($languageFiles as $file) {
                    $languagePack = $this->_registerLanguagePack($file);

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
    protected function _registerLanguagePack($file)
    {
        $xml = simplexml_load_file($file);
        $languageIso = $this->getLanguagePackIsoCode($file);
        if (!empty($xml)) {
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