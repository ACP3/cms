<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\I18n;

use ACP3\Installer\Core\Environment\ApplicationPath;

class Translator extends \ACP3\Core\I18n\Translator
{
    /**
     * @var \ACP3\Installer\Core\I18n\DictionaryCache
     */
    protected $dictionaryCache;

    /**
     * @param \ACP3\Installer\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Installer\Core\I18n\DictionaryCache $dictionaryCache
     * @param string $locale
     */
    public function __construct(
        ApplicationPath $appPath,
        DictionaryCache $dictionaryCache,
        $locale
    ) {
        $this->appPath = $appPath;
        $this->dictionaryCache = $dictionaryCache;
        $this->locale = $locale;
        $this->lang2Characters = \substr($this->locale, 0, \strpos($this->locale, '_'));
    }

    /**
     * @inheritdoc
     */
    public function languagePackExists($locale)
    {
        return !\preg_match('=/=', $locale)
            && \is_file($this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/' . $locale . '.xml') === true;
    }
}
