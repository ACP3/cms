<?php
namespace ACP3\Core\I18n;

use ACP3\Core\Config;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\I18n\DictionaryCache as LanguageCache;
use ACP3\Modules\ACP3\Users\Model\UserModel;

/**
 * Class Translator
 * @package ACP3\Core\I18n
 */
class Translator
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    protected $user;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\I18n\DictionaryCache
     */
    protected $dictionaryCache;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * Die zur Zeit eingestellte Sprache
     *
     * @var string
     */
    protected $locale = '';
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
     * @param \ACP3\Modules\ACP3\Users\Model\UserModel                        $user
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\I18n\DictionaryCache        $dictionaryCache
     * @param \ACP3\Core\Config                      $config
     */
    public function __construct(
        UserModel $user,
        ApplicationPath $appPath,
        LanguageCache $dictionaryCache,
        Config $config
    ) {
        $this->user = $user;
        $this->appPath = $appPath;
        $this->dictionaryCache = $dictionaryCache;
        $this->config = $config;
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert
     *
     * @param string $locale
     *
     * @return boolean
     */
    public function languagePackExists($locale)
    {
        return !preg_match('=/=', $locale)
        && is_file($this->appPath->getModulesDir() . 'ACP3/System/Resources/i18n/' . $locale . '.xml') === true;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        if ($this->locale === '') {
            $locale = $this->user->getLanguage();
            $this->locale = $this->languagePackExists($locale) === true ? $locale : $this->config->getSettings('system')['lang'];
        }

        return $this->locale;
    }

    /**
     * @return string
     */
    public function getShortIsoCode()
    {
        return substr($this->getLocale(), 0, strpos($this->getLocale(), '_'));
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
     */
    public function getDirection()
    {
        if (isset($this->buffer[$this->getLocale()]) === false) {
            $this->buffer[$this->getLocale()] = $this->dictionaryCache->getLanguageCache($this->getLocale());
        }

        return isset($this->buffer[$this->getLocale()]['info']['direction']) ? $this->buffer[$this->getLocale()]['info']['direction'] : 'ltr';
    }

    /**
     * @param string $module
     * @param string $phrase
     * @param array  $arguments
     *
     * @return string
     */
    public function t($module, $phrase, array $arguments = [])
    {
        if (isset($this->buffer[$this->getLocale()]) === false) {
            $this->buffer[$this->getLocale()] = $this->dictionaryCache->getLanguageCache($this->getLocale());
        }

        if (isset($this->buffer[$this->getLocale()]['keys'][$module . $phrase])) {
            return strtr($this->buffer[$this->getLocale()]['keys'][$module . $phrase], $arguments);
        }

        return strtoupper('{' . $module . '_' . $phrase . '}');
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
            $this->languagePacks = $this->dictionaryCache->getLanguagePacksCache();
        }

        $languages = $this->languagePacks;

        foreach ($languages as $key => $value) {
            $languages[$key]['selected'] = $languages[$key]['iso'] === $currentLanguage;
        }

        return $languages;
    }
}
