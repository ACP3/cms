<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\I18n;

use ACP3\Core\I18n\ExtractFromPathTrait;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Fisharebest\Localization\Locale;

/**
 * Class DictionaryCache
 * @package ACP3\Installer\Core\I18n
 */
class DictionaryCache
{
    use ExtractFromPathTrait;

    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * DictionaryCache constructor.
     *
     * @param \ACP3\Installer\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
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
        if (isset($this->buffer[$language]) === false) {
            $this->buffer[$language] = $this->setLanguageCache($language);
        }

        return $this->buffer[$language];
    }

    /**
     * Cacht die Sprachfiles, um diese schneller verarbeiten zu können
     *
     * @param string $language
     *
     * @return array
     */
    public function setLanguageCache($language)
    {
        $data = [];

        $languageFiles = glob($this->appPath->getInstallerModulesDir() . '*/Resources/i18n/' . $language . '.xml');
        foreach ($languageFiles as $file) {
            if (isset($data['info']['direction']) === false) {
                $locale = Locale::create($this->getLanguagePackIsoCode($file));
                $data['info']['direction'] = $locale->script()->direction();
            }

            $module = $this->getModuleFromPath($file);

            // Über die einzelnen Sprachstrings iterieren
            $xml = simplexml_load_file($file);
            foreach ($xml->keys->item as $item) {
                $data['keys'][strtolower($module . (string)$item['key'])] = trim((string)$item);
            }
        }

        return $data;
    }
}
