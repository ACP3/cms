<?php
namespace ACP3\Installer\Core\I18n;
use ACP3\Core\Filesystem;
use ACP3\Installer\Core\Environment\ApplicationPath;

/**
 * Class DictionaryCache
 * @package ACP3\Installer\Core\I18n
 */
class DictionaryCache
{
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * DictionaryCache constructor.
     *
     * @param \ACP3\Installer\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        $this->appPath = $appPath;
    }

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

        foreach (Filesystem::scandir($this->appPath->getInstallerModulesDir()) as $module) {
            $path = $this->appPath->getInstallerModulesDir() . $module . '/Resources/i18n/' . $language . '.xml';
            if (is_file($path) === true) {
                $xml = simplexml_load_file($path);
                if (isset($data['info']['direction']) === false) {
                    $data['info']['direction'] = (string)$xml->info->direction;
                }

                // Über die einzelnen Sprachstrings iterieren
                foreach ($xml->keys->item as $item) {
                    $data['keys'][strtolower($module . (string)$item['key'])] = trim((string)$item);
                }
            }
        }

        return $data;
    }
}