<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\I18n;


use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\DictionaryInterface;
use ACP3\Core\I18n\LocaleInterface;
use ACP3\Installer\Core\Environment\ApplicationPath;

class Locale implements LocaleInterface
{
    /**
     * @var ApplicationPath
     */
    private $appPath;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var DictionaryInterface
     */
    private $dictionary;
    /**
     * @var AvailableLanguagePacks
     */
    private $availableLanguagePacks;
    /**
     * @var string
     */
    private $locale = '';

    /**
     * Locale constructor.
     * @param ApplicationPath $appPath
     * @param RequestInterface $request
     * @param DictionaryInterface $dictionary
     * @param AvailableLanguagePacks $availableLanguagePacks
     */
    public function __construct(
        ApplicationPath $appPath,
        RequestInterface $request,
        DictionaryInterface $dictionary,
        AvailableLanguagePacks $availableLanguagePacks
    ) {
        $this->appPath = $appPath;
        $this->request = $request;
        $this->dictionary = $dictionary;
        $this->availableLanguagePacks = $availableLanguagePacks;
    }

    /**
     * @inheritdoc
     */
    public function getLocale(): string
    {
        if ($this->locale === '') {
            $this->setLocale();
        }

        return $this->locale;
    }

    private function setLocale()
    {
        $cookieLocale = $this->request->getCookies()->get('ACP3_INSTALLER_LANG', '');
        if (!preg_match('=/=', $cookieLocale)
            && is_file($this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/' . $cookieLocale . '.xml') === true
        ) {
            $this->locale = $cookieLocale;
        } else {
            $this->locale = 'en_US'; // Fallback language

            foreach ($this->request->getUserAgent()->parseAcceptLanguage() as $locale => $val) {
                $locale = str_replace('-', '_', $locale);
                if ($this->availableLanguagePacks->languagePackExists($locale) === true) {
                    $this->locale = $locale;
                    break;
                }
            }
        }
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
        return $this->dictionary->getDictionary($this->getLocale())['info']['direction'];
    }
}
