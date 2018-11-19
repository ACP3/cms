<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\I18n;

use ACP3\Core\I18n\DictionaryCacheInterface;
use ACP3\Installer\Core\Environment\ApplicationPath;

class Translator extends \ACP3\Core\I18n\Translator
{
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    private $appPath;

    /**
     * Translator constructor.
     *
     * @param \ACP3\Installer\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\I18n\DictionaryCacheInterface         $dictionaryCache
     */
    public function __construct(
        ApplicationPath $appPath,
        DictionaryCacheInterface $dictionaryCache
    ) {
        parent::__construct($appPath, $dictionaryCache);

        $this->appPath = $appPath;
    }

    /**
     * {@inheritdoc}
     */
    public function languagePackExists(string $locale)
    {
        return !\preg_match('=/=', $locale)
            && \is_file($this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/' . $locale . '.xml') === true;
    }
}
