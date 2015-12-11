<?php

namespace ACP3\Installer\Core\I18n;

/**
 * Class Lang
 * @package ACP3\Installer\Core
 */
class Translator extends \ACP3\Core\I18n\Translator
{
    /**
     * @var \ACP3\Installer\Core\I18n\DictionaryCache
     */
    protected $cache;

    /**
     * @param \ACP3\Installer\Core\I18n\DictionaryCache $cache
     * @param string                                    $locale
     */
    public function __construct(
        DictionaryCache $cache,
        $locale
    )
    {
        $this->cache = $cache;
        $this->locale = $locale;
        $this->lang2Characters = substr($this->locale, 0, strpos($this->locale, '_'));
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert
     *
     * @param string $locale
     *
     * @return boolean
     */
    public static function languagePackExists($locale)
    {
        return !preg_match('=/=',
            $locale) && is_file(INSTALLER_MODULES_DIR . 'Install/Resources/i18n/' . $locale . '.xml') === true;
    }
}
