<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Locale implements LocaleInterface
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var DictionaryCacheInterface
     */
    private $dictionaryCache;
    /**
     * @var string
     */
    private $locale = '';
    /**
     * @var string
     */
    private $direction = '';

    /**
     * Locale constructor.
     * @param SettingsInterface $settings
     * @param DictionaryCacheInterface $dictionaryCache
     */
    public function __construct(SettingsInterface $settings, DictionaryCacheInterface $dictionaryCache)
    {
        $this->settings = $settings;
        $this->dictionaryCache = $dictionaryCache;
    }

    /**
     * @inheritdoc
     */
    public function getLocale(): string
    {
        if ($this->locale === '') {
            $this->locale = $this->settings->getSettings(Schema::MODULE_NAME)['lang'];
        }

        return $this->locale;
    }

    /**
     * @inheritdoc
     */
    public function getShortIsoCode(): string
    {
        return substr($this->getLocale(), 0, strpos($this->getLocale(), '_'));
    }

    /**
     * @inheritdoc
     */
    public function getDirection(): string
    {
        if ($this->direction === '') {
            $this->direction = $this->dictionaryCache->getLanguageCache($this->getLocale())['info']['direction'];
        }

        return $this->direction;
    }
}
