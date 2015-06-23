<?php
namespace ACP3\Installer\Core\Lang;

/**
 * Class Cache
 * @package ACP3\Installer\Core\Lang
 */
class Cache
{
    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * Gibt die gecacheten Sprachstrings aus
     *
     * @param $language
     *
     * @return array
     */
    public function getLanguageCache($language)
    {
        if (isset($this->buffer[$language]) === false) {
            $this->buffer[$language] = $this->setLanguageCache($language);
        }

        return $this->buffer[$language];
    }

    /**
     * Cacht die Sprachfiles, um diese schneller verarbeiten zu können
     *
     * @param $language
     *
     * @return array
     */
    public function setLanguageCache($language)
    {
        $data = [];

        $modules = array_diff(scandir(INSTALLER_MODULES_DIR), ['.', '..']);

        foreach ($modules as $module) {
            $path = INSTALLER_MODULES_DIR . $module . '/Languages/' . $language . '.xml';
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