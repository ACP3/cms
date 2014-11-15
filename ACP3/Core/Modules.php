<?php
namespace ACP3\Core;

use ACP3\Modules\System;

/**
 * Class Modules
 * @package ACP3\Core
 */
class Modules
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Lang
     */
    protected $lang;
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var System\Model
     */
    protected $systemModel;
    /**
     * @var array
     */
    private $parseModules = [];
    /**
     * @var array
     */
    private $allModules = [];

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        Lang $lang
    )
    {
        $this->db = $db;
        $this->lang = $lang;
        $this->cache = new Cache('modules');
        $this->systemModel = new System\Model($db);
    }

    /**
     * Überprüft, ob eine Modulaktion überhaupt existiert
     *
     * @param string $path
     *
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
     *
     * @return boolean
     */
    public function isActive($module)
    {
        $info = $this->getModuleInfo($module);
        return !empty($info) && $info['active'] == 1;
    }

    /**
     * Durchläuft für das angeforderte Modul den <info> Abschnitt in der
     * module.xml und gibt die gefundenen Informationen als Array zurück
     *
     * @param string $module
     *
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
        return !empty($this->parseModules[$module]) ? $this->parseModules[$module] : [];
    }

    /**
     * @return string
     */
    protected function _getCacheKey()
    {
        return 'infos_' . $this->lang->getLanguage();
    }

    /**
     * Setzt den Cache für alle vorliegenden Modulinformationen
     */
    public function setModulesCache()
    {
        $infos = [];
        $dirs = array_diff(scandir(MODULES_DIR), array('.', '..'));
        foreach ($dirs as $dir) {
            $path = MODULES_DIR . '/' . $dir . '/config/module.xml';
            if (is_file($path) === true) {
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

    /**
     * Überprüft, ob ein Modul in der modules DB-Tabelle
     * eingetragen und somit installiert ist
     *
     * @param string $moduleName
     *
     * @return boolean
     */
    public function isInstalled($moduleName)
    {
        return $this->systemModel->moduleExists($moduleName);
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

}