<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\I18n;

use ACP3\Core\I18n\DictionaryInterface;
use ACP3\Core\I18n\ExtractFromPathTrait;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Fisharebest\Localization\Locale;

class Dictionary implements DictionaryInterface
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
     * @inheritdoc
     */
    public function getDictionary(string $locale): array
    {
        if (isset($this->buffer[$locale]) === false) {
            $this->saveDictionary($locale);
        }

        return $this->buffer[$locale];
    }

    /**
     * @inheritdoc
     */
    public function saveDictionary(string $locale): bool
    {
        $data = [];

        $languageFiles = glob($this->appPath->getInstallerModulesDir() . '*/Resources/i18n/' . $locale . '.xml');
        foreach ($languageFiles as $file) {
            if (isset($data['info']['direction']) === false) {
                $localeInfo = Locale::create($locale);
                $data['info']['direction'] = $localeInfo->script()->direction();
            }

            $module = $this->getModuleFromPath($file);

            // Über die einzelnen Sprachstrings iterieren
            $xml = simplexml_load_file($file);
            foreach ($xml->keys->item as $item) {
                $data['keys'][strtolower($module . (string)$item['key'])] = trim((string)$item);
            }
        }

        $this->buffer[$locale] = $data;

        return true;
    }
}
