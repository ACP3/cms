<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\I18n;


use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\DictionaryCacheInterface;
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
     * @var DictionaryCacheInterface
     */
    private $dictionaryCache;
    /**
     * @var AvailableLanguagePacks
     */
    private $availableLanguagePacks;
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
     * @param ApplicationPath $appPath
     * @param RequestInterface $request
     * @param DictionaryCacheInterface $dictionaryCache
     * @param AvailableLanguagePacks $availableLanguagePacks
     */
    public function __construct(
        ApplicationPath $appPath,
        RequestInterface $request,
        DictionaryCacheInterface $dictionaryCache,
        AvailableLanguagePacks $availableLanguagePacks
    ) {
        $this->appPath = $appPath;
        $this->request = $request;
        $this->dictionaryCache = $dictionaryCache;
        $this->availableLanguagePacks = $availableLanguagePacks;
    }

    /**
     * Gets the full locale name (e.g. en_US)
     *
     * @return string
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
     * Gets the short ISO language code (e.g en)
     *
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
