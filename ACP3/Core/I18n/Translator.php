<?php
namespace ACP3\Core\I18n;

class Translator implements TranslatorInterface
{
    /**
     * @var DictionaryCacheInterface
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
     * @param DictionaryCacheInterface $dictionaryCache
     * @param LocaleInterface $locale
     */
    public function __construct(
        DictionaryCacheInterface $dictionaryCache,
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
            $this->buffer[$this->locale->getLocale()] = $this->dictionaryCache->getDictionary($this->locale->getLocale());
        }

        if (isset($this->buffer[$this->locale->getLocale()]['keys'][$module . $phrase])) {
            return strtr($this->buffer[$this->locale->getLocale()]['keys'][$module . $phrase], $arguments);
        }

        return strtoupper('{' . $module . '_' . $phrase . '}');
    }
}
