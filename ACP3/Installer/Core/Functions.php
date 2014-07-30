<?php

namespace ACP3\Installer\Core;

use ACP3\Core;
use Doctrine\DBAL\Connection;

/**
 * Class Functions
 * @package ACP3\Installer\Core
 */
class Functions
{
    /**
     * @var Connection
     */
    protected $db;
    /**
     * @var Lang
     */
    protected $lang;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var Core\Validate
     */
    protected $validate;

    public function __construct(
        Connection $db,
        Lang $lang,
        Core\Modules $modules,
        Core\Validate $validate
    ) {
        $this->db = $db;
        $this->lang = $lang;
        $this->modules = $modules;
        $this->validate = $validate;
    }

    /**
     * Führt die Datenbankschema-Änderungen durch
     *
     * @param array $queries
     *    Array mit den durchzuführenden Datenbankschema-Änderungen
     * @param integer $version
     *    Version der Datenbank, auf welche aktualisiert werden soll
     * @return array
     */
    public function executeSqlQueries(array $queries, $version)
    {
        $bool = Core\Modules\AbstractInstaller::executeSqlQueries($queries);

        $result = array(
            'text' => sprintf($this->lang->t('update_db_version_to'), $version),
            'class' => $bool === true ? 'success' : 'important',
            'result_text' => $this->lang->t($bool === true ? 'db_update_success' : 'db_update_error')
        );

        return $result;
    }

    /**
     * Führt die Installationsanweisungen des jeweiligen Moduls durch
     *
     * @param string $module
     * @return boolean
     */
    public function installModule($module)
    {
        $bool = false;

        $module = ucfirst($module);
        $path = MODULES_DIR . $module . '/Installer.php';
        if (is_file($path) === true) {
            $className = Core\Modules\AbstractInstaller::buildClassName($module);
            /** @var Core\Modules\AbstractInstaller $installer */
            $installer = new $className(Core\Registry::get('db'));
            if ($installer instanceof Core\Modules\AbstractInstaller) {
                $bool = $installer->install();
            }
        }

        return $bool;
    }

    /**
     * Generiert das Dropdown-Menü mit der zur Verfügung stehenden Installersprachen
     *
     * @param string $selectedLanguage
     * @return array
     */
    public function languagesDropdown($selectedLanguage)
    {
        // Dropdown-Menü für die Sprachen
        $languages = array();
        $path = ACP3_ROOT_DIR . 'installation/languages/';
        $files = scandir($path);
        foreach ($files as $row) {
            if ($row !== '.' && $row !== '..') {
                $lang_info = Core\XML::parseXmlFile($path . $row, '/language/info');
                if (!empty($lang_info)) {
                    $languages[] = array(
                        'language' => substr($row, 0, -4),
                        'selected' => $selectedLanguage === substr($row, 0, -4) ? ' selected="selected"' : '',
                        'name' => $lang_info['name']
                    );
                }
            }
        }
        return $languages;
    }

    /**
     * Setzt die Ressourcen-Tabelle auf die Standardwerte zurück
     */
    public function resetResources($mode = 1)
    {
        $this->db->executeUpdate('TRUNCATE TABLE ' . DB_PRE . 'acl_resources');

        // Moduldaten in die ACL schreiben
        $modules = scandir(MODULES_DIR);
        foreach ($modules as $module) {
            $path = MODULES_DIR . $module . '/Installer.php';
            if ($module !== '.' && $module !== '..' && is_file($path) === true) {
                $className = Core\Modules\AbstractInstaller::buildClassName($module);
                /** @var Core\Modules\AbstractInstaller $installer */
                $installer = new $className(Core\Registry::get('Db'));
                $installer->addResources($mode);
            }
        }
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus
     *
     * @param string $module
     * @return integer
     */
    public function updateModule($module)
    {
        $result = false;

        $module = ucfirst($module);
        $path = MODULES_DIR . $module . '/Installer.php';
        if (is_file($path) === true) {
            $className = Core\Modules\AbstractInstaller::buildClassName($module);
            /** @var Core\Modules\AbstractInstaller $installer */
            $installer = new $className(Core\Registry::get('Db'));
            if ($installer instanceof Core\Modules\AbstractInstaller &&
                ($this->modules->isInstalled($module) || count($installer->renameModule()) > 0)
            ) {
                $result = $installer->updateSchema();
            }
        }

        return $result;
    }

    /**
     * Schreibt die Systemkonfigurationsdatei
     *
     * @param array $data
     * @return boolean
     */
    public function writeConfigFile(array $data)
    {
        $path = ACP3_DIR . 'config.php';
        if (is_writable($path) === true) {
            // Konfigurationsdatei in ein Array schreiben
            ksort($data);

            $content = "<?php\n";
            $content .= "define('INSTALLED', true);\n";

            $pattern = "define('CONFIG_%s', %s);\n";
            foreach ($data as $key => $value) {
                if (is_bool($value) === true) {
                    $value = $value === true ? 'true' : 'false';
                } elseif (is_numeric($value) !== true) {
                    $value = '\'' . $value . '\'';
                }
                $content .= sprintf($pattern, strtoupper($key), $value);
            }
            $bool = @file_put_contents($path, $content, LOCK_EX);
            return $bool !== false;
        }
        return false;
    }

}
