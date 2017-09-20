<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\I18n;

use ACP3\Installer\Core\Environment\ApplicationPath;

class AvailableLanguagePacks
{
    /**
     * @var ApplicationPath
     */
    private $appPath;

    /**
     * AvailableLanguagePacks constructor.
     * @param ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

    /**
     * Checks, whether the given locale exists as a language pack
     *
     * @param string $locale
     * @return boolean
     */
    public function languagePackExists(string $locale): bool
    {
        return !empty($locale)
            && !preg_match('=/=', $locale)
            && is_file($this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/' . $locale . '.xml') === true;
    }

    /**
     * @return string[] An array which contains the iso codes of the available language packs
     */
    public function getAll(): array
    {
        $languagePacks = [];
        foreach (glob($this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/*.xml') as $languagePack) {
            $languagePacks[] = substr($languagePack, strrpos($languagePack, '/') + 1, -4);
        }

        return $languagePacks;
    }
}
