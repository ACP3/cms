<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\I18n;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Translator
{
    private ?string $locale = null;
    /**
     * @var array<string, array{iso: string, name: string}>|null
     */
    private ?array $languagePacks = null;
    /**
     * @var array{info: array<string, string>, keys: array<string, string>}|null
     */
    private ?array $buffer = null;

    public function __construct(private DictionaryInterface $dictionary, private SettingsInterface $settings)
    {
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert.
     */
    public function languagePackExists(string $locale): bool
    {
        if ($this->languagePacks === null) {
            $this->languagePacks = $this->dictionary->getLanguagePacks();
        }

        $foundLanguagePack = array_filter($this->languagePacks, static fn ($languagePack) => $languagePack['iso'] === $locale);

        return !empty($foundLanguagePack);
    }

    public function getLocale(): string
    {
        if ($this->locale === null) {
            $this->locale = $this->settings->getSettings(Schema::MODULE_NAME)['lang'];
        }

        return $this->locale;
    }

    public function getShortIsoCode(): string
    {
        return substr($this->getLocale(), 0, strpos($this->getLocale(), '_'));
    }

    public function setLocale(string $locale): self
    {
        if ($this->languagePackExists($locale) === true) {
            $this->locale = $locale;
        }

        return $this;
    }

    /**
     * Gets the writing direction of the language.
     */
    public function getDirection(): string
    {
        if (isset($this->buffer[$this->getLocale()]) === false) {
            $this->buffer[$this->getLocale()] = $this->dictionary->getDictionary($this->getLocale());
        }

        return $this->buffer[$this->getLocale()]['info']['direction'] ?? 'ltr';
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public function t(string $module, string $phrase, array $arguments = []): string
    {
        if (isset($this->buffer[$this->getLocale()]) === false) {
            $this->buffer[$this->getLocale()] = $this->dictionary->getDictionary($this->getLocale());
        }

        $key = $module . $phrase;
        if (isset($this->buffer[$this->getLocale()]['keys'][$key])) {
            return strtr($this->buffer[$this->getLocale()]['keys'][$key], $arguments);
        }

        return strtoupper('{' . $module . '_' . $phrase . '}');
    }

    /**
     * Gets all currently available languages.
     *
     * @return array<string, array{iso: string, name: string}>
     */
    public function getLanguagePacks(): array
    {
        if ($this->languagePacks === null) {
            $this->languagePacks = $this->dictionary->getLanguagePacks();
        }

        return $this->languagePacks;
    }
}
