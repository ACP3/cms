<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

use ACP3\Core\Environment\ApplicationPath;

class Translator
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Core\I18n\DictionaryCacheInterface
     */
    private $dictionaryCache;
    /**
     * @var string
     */
    private $locale = '';
    /**
     * @var array
     */
    private $languagePacks = [];
    /**
     * @var array
     */
    private $buffer = [];

    public function __construct(
        ApplicationPath $appPath,
        DictionaryCacheInterface $dictionaryCache
    ) {
        $this->appPath = $appPath;
        $this->dictionaryCache = $dictionaryCache;
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function languagePackExists(string $locale)
    {
        return !\preg_match('=/=', $locale)
            && \is_file($this->appPath->getModulesDir() . 'ACP3/System/Resources/i18n/' . $locale . '.xml') === true;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getShortIsoCode()
    {
        return \substr($this->getLocale(), 0, \strpos($this->getLocale(), '_'));
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale(string $locale)
    {
        if ($this->languagePackExists($locale) === true) {
            $this->locale = $locale;
        }

        return $this;
    }

    /**
     * Gets the writing direction of the language.
     *
     * @return string
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getDirection(): string
    {
        if (isset($this->buffer[$this->getLocale()]) === false) {
            $this->buffer[$this->getLocale()] = $this->dictionaryCache->getLanguageCache($this->getLocale());
        }

        return isset($this->buffer[$this->getLocale()]['info']['direction']) ? $this->buffer[$this->getLocale()]['info']['direction'] : 'ltr';
    }

    /**
     * @param string $module
     * @param string $phrase
     * @param array  $arguments
     *
     * @return string
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function t($module, $phrase, array $arguments = [])
    {
        if (isset($this->buffer[$this->getLocale()]) === false) {
            $this->buffer[$this->getLocale()] = $this->dictionaryCache->getLanguageCache($this->getLocale());
        }

        if (isset($this->buffer[$this->getLocale()]['keys'][$module . $phrase])) {
            return \strtr($this->buffer[$this->getLocale()]['keys'][$module . $phrase], $arguments);
        }

        return \strtoupper('{' . $module . '_' . $phrase . '}');
    }

    /**
     * Gets all currently available languages.
     *
     * @param string $currentLanguage
     *
     * @return array
     */
    public function getLanguagePack(string $currentLanguage)
    {
        if (empty($this->languagePacks)) {
            $this->languagePacks = $this->dictionaryCache->getLanguagePacksCache();
        }

        $languages = $this->languagePacks;

        foreach ($languages as $key => $value) {
            $languages[$key]['selected'] = $languages[$key]['iso'] === $currentLanguage;
        }

        return $languages;
    }
}
