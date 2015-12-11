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
     * @param string                                    $lang
     */
    public function __construct(
        DictionaryCache $cache,
        $lang
    )
    {
        $this->cache = $cache;
        $this->lang = $lang;
        $this->lang2Characters = substr($this->lang, 0, strpos($this->lang, '_'));
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert
     *
     * @param string $lang
     *
     * @return boolean
     */
    public static function languagePackExists($lang)
    {
        return !preg_match('=/=', $lang) && is_file(INSTALLER_MODULES_DIR . 'Install/Resources/Languages/' . $lang . '.xml') === true;
    }
}
