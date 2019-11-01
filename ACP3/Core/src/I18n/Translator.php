<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

class Translator
{
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
        DictionaryCacheInterface $dictionaryCache
    ) {
        $this->dictionaryCache = $dictionaryCache;
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function languagePackExists(string $locale): bool
    {
        if (empty($this->languagePacks)) {
            $this->languagePacks = $this->dictionaryCache->getLanguagePacksCache();
        }

        $foundLanguagePack = \array_filter($this->languagePacks, static function ($languagePack) use ($locale) {
            return $languagePack['iso'] === $locale;
        });

        return !empty($foundLanguagePack);
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getShortIsoCode(): string
    {
        return \substr($this->getLocale(), 0, \strpos($this->getLocale(), '_'));
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): self
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
     */
    public function getDirection(): string
    {
        if (isset($this->buffer[$this->getLocale()]) === false) {
            $this->buffer[$this->getLocale()] = $this->dictionaryCache->getLanguageCache($this->getLocale());
        }

        return $this->buffer[$this->getLocale()]['info']['direction'] ?? 'ltr';
    }

    /**
     * @param string $module
     * @param string $phrase
     * @param array  $arguments
     *
     * @return string
     */
    public function t(string $module, string $phrase, array $arguments = []): string
    {
        if (isset($this->buffer[$this->getLocale()]) === false) {
            $this->buffer[$this->getLocale()] = $this->dictionaryCache->getLanguageCache($this->getLocale());
        }

        $key = $module . $phrase;
        if (isset($this->buffer[$this->getLocale()]['keys'][$key])) {
            return \strtr($this->buffer[$this->getLocale()]['keys'][$key], $arguments);
        }

        return \strtoupper('{' . $module . '_' . $phrase . '}');
    }

    /**
     * Gets all currently available languages.
     *
     * @param string $locale
     *
     * @return array
     */
    public function getLanguagePacks(string $locale): array
    {
        if (empty($this->languagePacks)) {
            $this->languagePacks = $this->dictionaryCache->getLanguagePacksCache();
        }

        $languages = $this->languagePacks;

        foreach ($languages as $key => $value) {
            $languages[$key]['selected'] = $languages[$key]['iso'] === $locale;
        }

        return $languages;
    }
}
