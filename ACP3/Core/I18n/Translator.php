<?php
namespace ACP3\Core\I18n;

use ACP3\Core\I18n\DictionaryCache as LanguageCache;

class Translator implements TranslatorInterface
{
    /**
     * @var \ACP3\Core\I18n\DictionaryCache
     */
    protected $dictionaryCache;
    /**
     * @var Locale
     */
    private $locale;
    /**
     * @var AvailableLanguagePacks
     */
    private $availableLanguagePacks;
    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * Translator constructor.
     * @param DictionaryCache $dictionaryCache
     * @param AvailableLanguagePacks $availableLanguagePacks
     * @param Locale $locale
     */
    public function __construct(
        LanguageCache $dictionaryCache,
        AvailableLanguagePacks $availableLanguagePacks,
        Locale $locale
    ) {
        $this->dictionaryCache = $dictionaryCache;
        $this->locale = $locale;
        $this->availableLanguagePacks = $availableLanguagePacks;
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert
     *
     * @param string $locale
     *
     * @return boolean
     * @deprecated
     */
    public function languagePackExists(string $locale): bool
    {
        return $this->availableLanguagePacks->languagePackExists($locale);
    }

    /**
     * @return string
     * @deprecated
     */
    public function getLocale(): string
    {
        return $this->locale->getLocale();
    }

    /**
     * @return string
     * @deprecated
     */
    public function getShortIsoCode(): string
    {
        return $this->locale->getShortIsoCode();
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        if ($this->languagePackExists($locale) === true) {
            $this->locale = $locale;
        }

        return $this;
    }

    /**
     * Gets the writing direction of the language
     *
     * @return string
     * @deprecated
     */
    public function getDirection(): string
    {
        return $this->locale->getDirection();
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
        if (isset($this->buffer[$this->locale->getLocale()]) === false) {
            $this->buffer[$this->locale->getLocale()] = $this->dictionaryCache->getLanguageCache($this->locale->getLocale());
        }

        if (isset($this->buffer[$this->locale->getLocale()]['keys'][$module . $phrase])) {
            return strtr($this->buffer[$this->locale->getLocale()]['keys'][$module . $phrase], $arguments);
        }

        return strtoupper('{' . $module . '_' . $phrase . '}');
    }
}
