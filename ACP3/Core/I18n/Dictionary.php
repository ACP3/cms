<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;

use ACP3\Core\Cache\Cache;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules\Vendor;
use Fisharebest\Localization\Locale;

class Dictionary implements DictionaryInterface
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
     * @param \ACP3\Core\Cache\Cache $cache
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
    public function getDictionary(string $locale): array
    {
        if ($this->cache->contains($locale) === false) {
            $this->saveDictionary($locale);
        }

        return $this->cache->fetch($locale);
    }

    /**
     * @inheritdoc
     */
    public function saveDictionary(string $locale): bool
    {
        $data = [];

        foreach ($this->vendors->getVendors() as $vendor) {
            $languageFiles = \glob($this->appPath->getModulesDir() . $vendor . '/*/Resources/i18n/' . $locale . '.xml');

            if ($languageFiles !== false) {
                foreach ($languageFiles as $file) {
                    if (isset($data['info']['direction']) === false) {
                        $localeInfo = Locale::create($locale);
                        $data['info']['direction'] = $localeInfo->script()->direction();
                    }

                    $module = $this->getModuleFromPath($file);

                    // Iterate over all language keys
                    $xml = \simplexml_load_file($file);
                    foreach ($xml->keys->item as $item) {
                        $data['keys'][\strtolower($module . (string)$item['key'])] = \trim((string)$item);
                    }
                }
            }
        }

        return $this->cache->save($locale, $data);
    }
}
