<?php
namespace ACP3\Core\I18n;

class Translator implements TranslatorInterface
{
    /**
     * @var DictionaryInterface
     */
    private $dictionary;
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
     * @param DictionaryInterface $dictionary
     * @param LocaleInterface $locale
     */
    public function __construct(
        DictionaryInterface $dictionary,
        LocaleInterface $locale
    ) {
        $this->dictionary = $dictionary;
        $this->locale = $locale;
    }

    /**
     * @inheritdoc
     */
    public function t(string $module, string $phrase, array $arguments = []): string
    {
        if (isset($this->buffer[$this->locale->getLocale()]) === false) {
            $this->buffer[$this->locale->getLocale()] = $this->dictionary->getDictionary($this->locale->getLocale());
        }

        if (isset($this->buffer[$this->locale->getLocale()]['keys'][$module . $phrase])) {
            return \strtr($this->buffer[$this->locale->getLocale()]['keys'][$module . $phrase], $arguments);
        }

        return \strtoupper('{' . $module . '_' . $phrase . '}');
    }
}
