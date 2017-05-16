<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\I18n;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Locale
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var DictionaryCache
     */
    private $dictionaryCache;
    /**
     * @var string
     */
    private $locale = 'en_US';
    /**
     * @var string
     */
    private $direction = 'ltr';

    /**
     * Locale constructor.
     * @param SettingsInterface $settings
     * @param DictionaryCache $dictionaryCache
     */
    public function __construct(SettingsInterface $settings, DictionaryCache $dictionaryCache)
    {
        $this->settings = $settings;
        $this->dictionaryCache = $dictionaryCache;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        if ($this->locale === '') {
            $this->locale = $this->settings->getSettings(Schema::MODULE_NAME)['lang'];
        }

        return $this->locale;
    }

    /**
     * @return string
     */
    public function getShortIsoCode(): string
    {
        return substr($this->getLocale(), 0, strpos($this->getLocale(), '_'));
    }

    /**
     * Gets the writing direction of the language
     *
     * @return string
     */
    public function getDirection(): string
    {
        if ($this->direction === '') {
            $this->direction = $this->dictionaryCache->getLanguageCache($this->getLocale())['info']['direction'];
        }

        return $this->direction;
    }
}
