<?php
namespace ACP3\Core;

use ACP3\Modules\System;

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
    private $parseModules = array();
    /**
     * @var array
     */
    private $allModules = array();
    /**
     * @var ACL
     */
    protected $acl;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Lang
     */
    protected $lang;
    /**
     * @var Cache2
     */
    protected $cache;
    /**
     * @var System\Model
     */
    protected $systemModel;

    public function __construct(\Doctrine\DBAL\Connection $db, ACL $acl, Lang $lang)
    {
        $this->db = $db;
        $this->acl = $acl;
        $this->lang = $lang;
        $this->cache = new Cache2('modules');
        $this->systemModel = new System\Model($db);
    }

    /**
     * Überpüft, ob eine Modulaktion existiert und der Benutzer darauf Zugriff hat
     *
     * @param string $path
     *    Zu überprüfendes Modul
     *
     * @return integer
     */
    public function hasPermission($path)
    {
        if ($this->actionExists($path) === true) {
            $pathArray = explode('/', $path);

            if ($this->isActive($pathArray[1]) === true) {
                return $this->acl->canAccessResource($path);
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
    public function actionExists($path)
    {
        $pathArray = array_map(function ($value) {
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
    public function isActive($module)
    {
        $info = $this->getModuleInfo($module);
        return !empty($info) && $info['active'] == 1;
    }

    /**
     * Überprüft, ob ein Modul in der modules DB-Tabelle
     * eingetragen und somit installiert ist
     *
     * @param string $moduleName
     * @return boolean
     */
    public function isInstalled($moduleName)
    {
        return $this->systemModel->moduleExists($moduleName);
    }

    /**
     * Gibt ein alphabetisch sortiertes Array mit allen gefundenen
     * Modulen des ACP3 mitsamt Modulinformationen aus
     *
     * @return mixed
     */
    public function getAllModules()
    {
        if (empty($this->allModules)) {
            $dir = array_diff(scandir(MODULES_DIR), array('.', '..'));
            foreach ($dir as $module) {
                $info = $this->getModuleInfo($module);
                if (!empty($info)) {
                    $this->allModules[$info['name']] = $info;
                }
            }
            ksort($this->allModules);
        }

        return $this->allModules;
    }

    /**
     * Gibt alle derzeit aktiven Module in einem Array zurück
     *
     * @return array
     */
    public function getActiveModules()
    {
        $activeModules = $this->getAllModules();

        foreach ($this->allModules as $key => $values) {
            if ($values['active'] === true) {
                $activeModules[$key] = $values;
            }
        }

        return $activeModules;
    }

    /**
     * @return string
     */
    protected function _getCacheKey()
    {
        return 'infos_' . $this->lang->getLanguage();
    }

    /**
     * Durchläuft für das angeforderte Modul den <info> Abschnitt in der
     * module.xml und gibt die gefundenen Informationen als Array zurück
     *
     * @param string $module
     * @return array
     */
    public function getModuleInfo($module)
    {
        $module = strtolower($module);
        if (empty($this->parseModules)) {
            $filename = $this->_getCacheKey();
            if ($this->cache->contains($filename) === false) {
                $this->setModulesCache();
            }
            $this->parseModules = $this->cache->fetch($filename);
        }
        return !empty($this->parseModules[$module]) ? $this->parseModules[$module] : array();
    }

    /**
     * Setzt den Cache für alle vorliegenden Modulinformationen
     */
    public function setModulesCache()
    {
        $infos = array();
        $dirs = scandir(MODULES_DIR);
        foreach ($dirs as $dir) {
            $path = MODULES_DIR . '/' . $dir . '/config/module.xml';
            if ($dir !== '.' && $dir !== '..' && is_file($path) === true) {
                $moduleInfo = XML::parseXmlFile($path, 'info');

                if (!empty($moduleInfo)) {
                    $moduleName = strtolower($dir);
                    $moduleInfoDb = $this->systemModel->getInfoByModuleName($moduleName);
                    $infos[$moduleName] = array(
                        'id' => !empty($moduleInfoDb) ? $moduleInfoDb['id'] : 0,
                        'dir' => $dir,
                        'active' => !empty($moduleInfoDb) && $moduleInfoDb['active'] == 1 ? true : false,
                        'schema_version' => !empty($moduleInfoDb) ? (int)$moduleInfoDb['version'] : 0,
                        'description' => isset($moduleInfo['description']['lang']) && $moduleInfo['description']['lang'] === 'true' ? $this->lang->t($moduleName, 'mod_description') : $moduleInfo['description']['lang'],
                        'author' => $moduleInfo['author'],
                        'version' => isset($moduleInfo['version']['core']) && $moduleInfo['version']['core'] === 'true' ? CONFIG_VERSION : $moduleInfo['version'],
                        'name' => isset($moduleInfo['name']['lang']) && $moduleInfo['name']['lang'] == 'true' ? $this->lang->t($moduleName, $moduleName) : $moduleInfo['name'],
                        'categories' => isset($moduleInfo['categories']) ? true : false,
                        'protected' => isset($moduleInfo['protected']) ? true : false,
                    );
                    $infos[$moduleName]['dependencies'] = array_values(XML::parseXmlFile($path, 'info/dependencies'));
                }
            }
        }

        $this->cache->save($this->_getCacheKey(), $infos);
    }

}