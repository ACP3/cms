<?php

namespace ACP3\Installer\Core;

/**
 * Stellt Funktionen bereit, um das ACP3 in verschiendene Sprachen zu übersetzen
 *
 * @author Tino Goratsch
 */
class Lang extends \ACP3\Core\Lang
{

    function __construct($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Cached die Sprachfiles, um diese schneller verarbeiten zu können
     */
    protected function parseLanguageFile()
    {
        $data = array();
        $path = ACP3_ROOT_DIR . 'installation/languages/' . $this->lang . '.xml';
        if (is_file($path) === true) {
            $xml = simplexml_load_file($path);
            // Über die einzelnen Sprachstrings iterieren
            foreach ($xml->strings[0]->item as $item) {
                $data[(string)$item['key']] = (string)$item;
            }
        }
        return $data;
    }

    /**
     * Gibt den angeforderten Sprachstring aus
     *
     * @param string $key
     * @return string
     */
    public function t($key, $unused = '')
    {
        static $lang_data = array();

        if (empty($lang_data)) {
            $lang_data = $this->parseLanguageFile();
        }

        return isset($lang_data[$key]) ? $lang_data[$key] : strtoupper('{' . $key . '}');
    }

    /**
     * Überprüft, ob das angegebene Sprachpaket existiert
     *
     * @param string $lang
     * @return boolean
     */
    public static function languagePackExists($lang)
    {
        return !preg_match('=/=', $lang) && is_file(ACP3_ROOT_DIR . 'installation/languages/' . $lang . '.xml') === true;
    }

}