<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     * @var DictionaryInterface
     */
    private $dictionary;

    /**
     * Locale constructor.
     * @param SettingsInterface $settings
     * @param DictionaryInterface $dictionary
     */
    public function __construct(SettingsInterface $settings, DictionaryInterface $dictionary)
    {
        $this->settings = $settings;
        $this->dictionary = $dictionary;
    }

    /**
     * @inheritdoc
     */
    public function getLocale(): string
    {
        return $this->settings->getSettings(Schema::MODULE_NAME)['lang'];
    }

    /**
     * @inheritdoc
     */
    public function getShortIsoCode(): string
    {
        return \substr($this->getLocale(), 0, \strpos($this->getLocale(), '_'));
    }

    /**
     * @inheritdoc
     */
    public function getDirection(): string
    {
        return $this->dictionary->getDictionary($this->getLocale())['info']['direction'];
    }
}
