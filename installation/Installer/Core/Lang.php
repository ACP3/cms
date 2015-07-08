<?php

namespace ACP3\Installer\Core;

use ACP3\Installer\Core\Lang\Cache;

/**
 * Class Lang
 * @package ACP3\Installer\Core
 */
class Lang extends \ACP3\Core\Lang
{
    /**
     * @var \ACP3\Installer\Core\Lang\Cache
     */
    protected $cache;

    /**
     * @param \ACP3\Installer\Core\Lang\Cache $cache
     * @param string                          $lang
     */
    public function __construct(
        Cache $cache,
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
        return !preg_match('=/=', $lang) && is_file(INSTALLER_MODULES_DIR . 'Install/Languages/' . $lang . '.xml') === true;
    }

    /**
     * Gibt den angeforderten Sprachstring aus
     *
     * @param string $module
     * @param string $key
     *
     * @return string
     */
    public function t($module, $key)
    {
        if (empty($this->buffer[$this->lang])) {
            $this->buffer[$this->lang] = $this->cache->getLanguageCache($this->lang);
        }

        return isset($this->buffer[$this->lang]['keys'][$module][$key]) ? $this->buffer[$this->lang]['keys'][$module][$key] : strtoupper('{' . $module . '_' . $key . '}');
    }
}
