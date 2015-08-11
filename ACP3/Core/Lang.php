<?php
namespace ACP3\Core;

use ACP3\Core\Lang\Cache as LanguageCache;

/**
 * Class Lang
 * @package ACP3\Core
 */
class Lang
{
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Lang\Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * Die zur Zeit eingestellte Sprache
     *
     * @var string
     */
    protected $lang = '';
    /**
     * @var string
     */
    protected $lang2Characters = '';
    /**
     * @var array
     */
    protected $languagePacks = [];
    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * @param \ACP3\Core\Auth       $auth
     * @param \ACP3\Core\Lang\Cache $cache
     * @param \ACP3\Core\Config     $config
     */
    public function __construct(
        Auth $auth,
        LanguageCache $cache,
        Config $config
    )
    {
        $this->auth = $auth;
        $this->cache = $cache;
        $this->config = $config;
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
        return !preg_match('=/=', $lang) && is_file(MODULES_DIR . 'ACP3/System/Languages/' . $lang . '.xml') === true;
    }

    /**
     * Parst den ACCEPT-LANGUAGE Header des Browsers
     * und selektiert die präferierte Sprache
     *
     * @return string
     */
    final public static function parseAcceptLanguage()
    {
        $languages = [];

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $matches = [];
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);

            if (!empty($matches[1])) {
                $languages = array_combine($matches[1], $matches[4]);

                // Für Einträge ohne q-Faktor, Wert auf 1 setzen
                foreach ($languages as $lang => $val) {
                    if ($val === '') {
                        $languages[$lang] = 1;
                    }
                }

                // Liste nach Sprachpräferenz sortieren
                arsort($languages, SORT_NUMERIC);
            }
        }

        // Über die Sprachen iterieren und das passende Sprachpaket auswählen
        foreach ($languages as $lang => $val) {
            if (self::languagePackExists($lang) === true) {
                return $lang;
            }
        }

        return 'en_US';
    }

    /**
     * Gibt die aktuell eingestellte Sprache zurück
     *
     * @return string
     */
    public function getLanguage()
    {
        if ($this->lang === '') {
            $lang = $this->auth->getUserLanguage();
            $this->lang = self::languagePackExists($lang) === true ? $lang : $this->config->getSettings('system')['lang'];
        }

        return $this->lang;
    }

    /**
     * @return string
     */
    public function getLanguage2Characters()
    {
        return substr($this->getLanguage(), 0, strpos($this->getLanguage(), '_'));
    }

    /**
     * Verändert die aktuell eingestellte Sprache
     *
     * @param string $lang
     *
     * @return $this
     */
    public function setLanguage($lang)
    {
        if (self::languagePackExists($lang) === true) {
            $this->lang = $lang;
        }

        return $this;
    }

    /**
     * Gets the writing direction of the language
     *
     * @return string
     */
    public function getDirection()
    {
        if (isset($this->buffer[$this->lang]) === false) {
            $this->buffer[$this->lang] = $this->cache->getLanguageCache($this->getLanguage());
        }

        return isset($this->buffer[$this->lang]['info']['direction']) ? $this->buffer[$this->lang]['info']['direction'] : 'ltr';
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
        if (isset($this->buffer[$this->lang]) === false) {
            $this->buffer[$this->lang] = $this->cache->getLanguageCache($this->getLanguage());
        }

        return isset($this->buffer[$this->lang]['keys'][$module][$key]) ? $this->buffer[$this->lang]['keys'][$module][$key] : strtoupper('{' . $module . '_' . $key . '}');
    }

    /**
     * Gets all currently available languages
     *
     * @param string $currentLanguage
     *
     * @return array
     */
    public function getLanguagePack($currentLanguage)
    {
        if (empty($this->languagePacks)) {
            $this->languagePacks = $this->cache->getLanguagePacksCache();
        }

        $languages = $this->languagePacks;

        foreach ($languages as $key => $value) {
            $languages[$key]['selected'] = $languages[$key]['iso'] === $currentLanguage;
        }

        return $languages;
    }

}
