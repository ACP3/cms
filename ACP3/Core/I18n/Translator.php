<?php
namespace ACP3\Core\I18n;

use ACP3\Core\I18n\DictionaryCache as LanguageCache;

class Translator implements TranslatorInterface
{
    /**
     * @var \ACP3\Core\I18n\DictionaryCache
     */
    private $dictionaryCache;
    /**
     * @var LocaleInterface
     */
    private $locale;
    /**
     * @var array
     */
    private $buffer = [];

    /**
     * Translator constructor.
     * @param DictionaryCache $dictionaryCache
     * @param LocaleInterface $locale
     */
    public function __construct(
        LanguageCache $dictionaryCache,
        LocaleInterface $locale
    ) {
        $this->dictionaryCache = $dictionaryCache;
        $this->locale = $locale;
    }

    /**
     * @inheritdoc
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
