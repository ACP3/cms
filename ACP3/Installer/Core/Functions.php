<?php

namespace ACP3\Installer\Core;

use ACP3\Core;

/**
 * Manages the most used function of the installer
 *
 * @author Tino Goratsch
 */
abstract class Functions
{

    /**
     * Gibt eine Box mit den aufgetretenen Fehlern aus
     *
     * @param string|array $errors
     * @return string
     */
    public static function errorBox($errors)
    {
        $non_integer_keys = false;
        if (is_array($errors) === true) {
            foreach (array_keys($errors) as $key) {
                if (Core\Validate::isNumber($key) === false) {
                    $non_integer_keys = true;
                    break;
                }
            }
        } else {
            $errors = (array)$errors;
        }
        Core\Registry::get('View')->assign('error_box', array('non_integer_keys' => $non_integer_keys, 'errors' => $errors));
        return Core\Registry::get('View')->fetch('error_box.tpl');
    }

    /**
     * Führt die Datenbankschema-Änderungen durch
     *
     * @param array $queries
     *    Array mit den durchzuführenden Datenbankschema-Änderungen
     * @param integer $version
     *    Version der Datenbank, auf welche aktualisiert werden soll
     */
    public static function executeSqlQueries(array $queries, $version)
    {
        $bool = Core\Modules\Installer::executeSqlQueries($queries);

        $result = array(
            'text' => sprintf(Core\Registry::get('Lang')->t('update_db_version_to'), $version),
            'class' => $bool === true ? 'success' : 'important',
            'result_text' => Core\Registry::get('Lang')->t($bool === true ? 'db_update_success' : 'db_update_error')
        );

        return $result;
    }

    /**
     * Führt die Installationsanweisungen des jeweiligen Moduls durch
     *
     * @param string $module
     * @return boolean
     */
    public static function installModule($module)
    {
        $bool = false;

        $module = ucfirst($module);
        $path = MODULES_DIR . $module . '/Installer.php';
        if (is_file($path) === true) {
            $className = Core\Modules\Installer::buildClassName($module);
            $install = new $className();
            if ($install instanceof Core\Modules\Installer) {
                $bool = $install->install();
            }
        }

        return $bool;
    }

    /**
     * Generiert das Dropdown-Menü mit der zur Verfügung stehenden Installersprachen
     *
     * @param string $selected_language
     * @return array
     */
    public static function languagesDropdown($selected_language)
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
                        'selected' => $selected_language === substr($row, 0, -4) ? ' selected="selected"' : '',
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
    public static function resetResources($mode = 1)
    {
        Core\Registry::get('Db')->executeUpdate('TRUNCATE TABLE ' . DB_PRE . 'acl_resources');

        // Moduldaten in die ACL schreiben
        $modules = scandir(MODULES_DIR);
        foreach ($modules as $module) {
            $path = MODULES_DIR . $module . '/Installer.php';
            if ($module !== '.' && $module !== '..' && is_file($path) === true) {
                $className = Core\Modules\Installer::buildClassName($module);
                $install = new $className();
                $install->addResources($mode);
            }
        }
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus
     *
     * @param string $module
     * @return integer
     */
    public static function updateModule($module)
    {
        $result = false;

        $module = ucfirst($module);
        $path = MODULES_DIR . $module . '/Installer.php';
        if (is_file($path) === true) {
            $className = Core\Modules\Installer::buildClassName($module);
            $install = new $className();
            if ($install instanceof Core\Modules\Installer &&
                (\ACP3\Core\Modules::isInstalled($module) || count($install->renameModule()) > 0)
            ) {
                $result = $install->updateSchema();
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
    public static function writeConfigFile(array $data)
    {
        $path = ACP3_DIR . 'config.php';
        if (is_writable($path) === true) {
            // Konfigurationsdatei in ein Array schreiben
            ksort($data);

            $content = "<?php\n";
            $content .= "define('INSTALLED', true);\n";
            if (defined('DEBUG') === true) {
                $content .= "define('DEBUG', " . ((bool)DEBUG === true ? 'true' : 'false') . ");\n";
            }
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
            return $bool !== false ? true : false;
        }
        return false;
    }

}
