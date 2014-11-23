<?php

namespace ACP3\Installer\Core;

/**
 * Class Lang
 * @package ACP3\Installer\Core
 */
class Lang extends \ACP3\Core\Lang
{

    /**
     * @param \ACP3\Core\Auth $lang
     */
    function __construct($lang)
    {
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
        if (empty($this->buffer)) {
            $this->buffer = $this->getLanguageCache();
        }

        return isset($this->buffer['keys'][$module][$key]) ? $this->buffer['keys'][$module][$key] : strtoupper('{' . $module . '_' . $key . '}');
    }

    /**
     * Gibt die gecacheten Sprachstrings aus
     *
     * @return array
     */
    protected function getLanguageCache()
    {
        if (empty($this->buffer) === true) {
            $this->buffer = $this->setLanguageCache();
        }

        return $this->buffer;
    }

    /**
     * Cacht die Sprachfiles, um diese schneller verarbeiten zu können
     */
    public function setLanguageCache()
    {
        $data = [];

        $modules = array_diff(scandir(INSTALLER_MODULES_DIR), ['.', '..']);

        foreach ($modules as $module) {
            $path = INSTALLER_MODULES_DIR . $module . '/Languages/' . $this->lang . '.xml';
            if (is_file($path) === true) {
                $xml = simplexml_load_file($path);
                if (isset($data['info']['direction']) === false) {
                    $data['info']['direction'] = (string)$xml->info->direction;
                }

                // Über die einzelnen Sprachstrings iterieren
                foreach ($xml->keys->item as $item) {
                    $data['keys'][strtolower($module)][(string)$item['key']] = trim((string)$item);
                }
            }
        }

        return $data;
    }

}