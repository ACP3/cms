<?php

namespace ACP3\Installer\Core\I18n;

use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Installer\Core\Environment\ApplicationPath;

/**
 * Class Lang
 * @package ACP3\Installer\Core
 */
class Translator implements TranslatorInterface
{
    /**
     * @var \ACP3\Installer\Core\I18n\DictionaryCache
     */
    protected $dictionaryCache;
    /**
     * @var array
     */
    private $buffer = [];

    /**
     * @param \ACP3\Installer\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Installer\Core\I18n\DictionaryCache $dictionaryCache
     * @param string $locale
     */
    public function __construct(
        ApplicationPath $appPath,
        DictionaryCache $dictionaryCache,
        $locale
    ) {
        $this->appPath = $appPath;
        $this->dictionaryCache = $dictionaryCache;
        $this->locale = $locale;
        $this->lang2Characters = substr($this->locale, 0, strpos($this->locale, '_'));
    }

    /**
     * @inheritdoc
     */
    public function languagePackExists($locale)
    {
        return !preg_match('=/=', $locale)
            && is_file($this->appPath->getInstallerModulesDir() . 'Install/Resources/i18n/' . $locale . '.xml') === true;
    }

    /**
     * Translates a given phrase of a given module into some language
     *
     * @param string $module
     * @param string $phrase
     * @param array $arguments
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
