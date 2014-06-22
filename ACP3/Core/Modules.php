<?php
namespace ACP3\Core;

/**
 * Klasse für die Module
 *
 * @author Tino Goratsch
 */
class Modules
{
    /**
     * @var array
     */
    private static $parseModules = array();

    /**
     * @var array
     */
    private static $allModules = array();

    /**
     * Überpüft, ob eine Modulaktion existiert und der Benutzer darauf Zugriff hat
     *
     * @param string $path
     *    Zu überprüfendes Modul
     *
     * @return integer
     */
    public static function hasPermission($path)
    {
        if (self::actionExists($path) === true) {
            $pathArray = explode('/', $path);

            if (self::isActive($pathArray[1]) === true) {
                return Registry::get('ACL')->canAccessResource($path);
            }
        }
        return 0;
    }

    /**
     * Überprüft, ob eine Modulaktion überhaupt existiert
     *
     * @param string $path
     * @return boolean
     */
    public static function actionExists($path)
    {
        $pathArray = array_map(function($value) {
            return str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $value))));
        }, explode('/', $path));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'Index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'Index';
        }

        if ($pathArray[0] !== 'Frontend') {
            $className = "\\ACP3\\Modules\\$pathArray[1]\\Controller\\$pathArray[0]\\$pathArray[2]";
        } else {
            $className = "\\ACP3\\Modules\\$pathArray[1]\\Controller\\$pathArray[2]";
        }

        return method_exists($className, 'action' . $pathArray[3]);
    }

    /**
     * Gibt zurück, ob ein Modul aktiv ist oder nicht
     *
     * @param string $module
     * @return boolean
     */
    public static function isActive($module)
    {
        $info = self::getModuleInfo($module);
        return !empty($info) && $info['active'] == 1;
    }

    /**
     * Überprüft, ob ein Modul in der modules DB-Tabelle
     * eingetragen und somit installiert ist
     *
     * @param string $module
     * @return boolean
     */
    public static function isInstalled($module)
    {
        return Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'modules WHERE name = ?', array($module)) == 1;
    }

    /**
     * Gibt ein alphabetisch sortiertes Array mit allen gefundenen
     * Modulen des ACP3 mitsamt Modulinformationen aus
     *
     * @param bool $onlyActiveModules
     * @return mixed
     */
    public static function getAllModules($onlyActiveModules = false)
    {
        if (empty(static::$allModules)) {
            $dir = scandir(MODULES_DIR);
            foreach ($dir as $module) {
                if ($module !== '.' && $module !== '..') {
                    $info = self::getModuleInfo($module);
                    if (!empty($info) && ($onlyActiveModules === false || ($onlyActiveModules === true && self::isActive($module) === true))) {
                        static::$allModules[$info['name']] = $info;
                    }
                }
            }
            ksort(static::$allModules);
        }

        return static::$allModules;
    }

    /**
     * Gibt alle derzeit aktiven Module in einem Array zurück
     *
     * @return array
     */
    public static function getActiveModules()
    {
        return self::getAllModules(true);
    }

    /**
     * Durchläuft für das angeforderte Modul den <info> Abschnitt in der
     * module.xml und gibt die gefundenen Informationen als Array zurück
     *
     * @param string $module
     * @return array
     */
    public static function getModuleInfo($module)
    {
        $module = strtolower($module);
        if (empty(static::$parseModules)) {
            $filename = 'infos_' . Registry::get('Lang')->getLanguage();
            if (Cache::check($filename, 'modules') === false) {
                self::setModulesCache();
            }
            static::$parseModules = Cache::output($filename, 'modules');
        }
        return !empty(static::$parseModules[$module]) ? static::$parseModules[$module] : array();
    }

    /**
     * Setzt den Cache für alle vorliegenden Modulinformationen
     */
    public static function setModulesCache()
    {
        $infos = array();
        $dirs = scandir(MODULES_DIR);
        foreach ($dirs as $dir) {
            $path = MODULES_DIR . '/' . $dir . '/module.xml';
            if ($dir !== '.' && $dir !== '..' && is_file($path) === true) {
                $moduleInfo = XML::parseXmlFile($path, 'info');

                if (!empty($moduleInfo)) {
                    $moduleName = strtolower($dir);
                    $moduleInfoDb = Registry::get('Db')->fetchAssoc('SELECT id, version, active FROM ' . DB_PRE . 'modules WHERE name = ?', array($moduleName));
                    $infos[$moduleName] = array(
                        'id' => !empty($moduleInfoDb) ? $moduleInfoDb['id'] : 0,
                        'dir' => $dir,
                        'active' => !empty($moduleInfoDb) && $moduleInfoDb['active'] == 1 ? true : false,
                        'schema_version' => !empty($moduleInfoDb) ? (int)$moduleInfoDb['version'] : 0,
                        'description' => isset($moduleInfo['description']['lang']) && $moduleInfo['description']['lang'] === 'true' ? Registry::get('Lang')->t($moduleName, 'mod_description') : $moduleInfo['description']['lang'],
                        'author' => $moduleInfo['author'],
                        'version' => isset($moduleInfo['version']['core']) && $moduleInfo['version']['core'] === 'true' ? CONFIG_VERSION : $moduleInfo['version'],
                        'name' => isset($moduleInfo['name']['lang']) && $moduleInfo['name']['lang'] == 'true' ? Registry::get('Lang')->t($moduleName, $moduleName) : $moduleInfo['name'],
                        'categories' => isset($moduleInfo['categories']) ? true : false,
                        'protected' => isset($moduleInfo['protected']) ? true : false,
                    );
                    $infos[$moduleName]['dependencies'] = array_values(XML::parseXmlFile($path, 'info/dependencies'));
                }
            }
        }
        Cache::create('infos_' . Registry::get('Lang')->getLanguage(), $infos, 'modules');
    }

}