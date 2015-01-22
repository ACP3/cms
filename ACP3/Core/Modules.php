<?php
namespace ACP3\Core;

use ACP3\Modules\System;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class Modules
 * @package ACP3\Core
 */
class Modules
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var \ACP3\Core\Cache
     */
    protected $modulesCache;
    /**
     * @var \ACP3\Modules\System\Model
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

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     * @param \ACP3\Core\Lang                                  $lang
     * @param \ACP3\Core\XML                                   $xml
     * @param \ACP3\Core\Cache                                 $modulesCache
     * @param \ACP3\Modules\System\Model                       $systemModel
     */
    public function __construct(
        Container $container,
        Lang $lang,
        XML $xml,
        Cache $modulesCache,
        System\Model $systemModel
    )
    {
        $this->container = $container;
        $this->lang = $lang;
        $this->xml = $xml;
        $this->modulesCache = $modulesCache;
        $this->systemModel = $systemModel;
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
            return str_replace(' ', '', strtolower(str_replace('_', ' ', $value)));
        }, explode('/', $path));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        $serviceId = $pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2];

        if ($this->container->has($serviceId)) {
            return method_exists($this->container->get($serviceId), 'action' . $pathArray[3]);
        }

        return false;
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
        return !empty($info) && $info['active'] === true;
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
            if ($this->modulesCache->contains($filename) === false) {
                $this->setModulesCache();
            }
            $this->parseModules = $this->modulesCache->fetch($filename);
        }
        return !empty($this->parseModules[$module]) ? $this->parseModules[$module] : [];
    }

    /**
     * @param string $module
     *
     * @return integer
     */
    public function getModuleId($module)
    {
        return $this->getModuleInfo($module)['id'];
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
        $dirs = array_diff(scandir(MODULES_DIR), ['.', '..']);
        foreach ($dirs as $dir) {
            $path = MODULES_DIR . '/' . $dir . '/config/module.xml';
            if (is_file($path) === true) {
                $moduleInfo = $this->xml->parseXmlFile($path, 'info');

                if (!empty($moduleInfo)) {
                    $moduleName = strtolower($dir);
                    $moduleInfoDb = $this->systemModel->getInfoByModuleName($moduleName);
                    $infos[$moduleName] = [
                        'id' => !empty($moduleInfoDb) ? $moduleInfoDb['id'] : 0,
                        'dir' => $dir,
                        'installed' => (!empty($moduleInfoDb)),
                        'active' => (!empty($moduleInfoDb) && $moduleInfoDb['active'] == 1),
                        'schema_version' => !empty($moduleInfoDb) ? (int)$moduleInfoDb['version'] : 0,
                        'description' => isset($moduleInfo['description']['lang']) && $moduleInfo['description']['lang'] === 'true' ? $this->lang->t($moduleName, 'mod_description') : $moduleInfo['description']['lang'],
                        'author' => $moduleInfo['author'],
                        'version' => $moduleInfo['version'],
                        'name' => isset($moduleInfo['name']['lang']) && $moduleInfo['name']['lang'] == 'true' ? $this->lang->t($moduleName, $moduleName) : $moduleInfo['name'],
                        'categories' => isset($moduleInfo['categories']),
                        'protected' => isset($moduleInfo['protected']),
                    ];
                    $infos[$moduleName]['dependencies'] = array_values($this->xml->parseXmlFile($path, 'info/dependencies'));
                }
            }
        }

        $this->modulesCache->save($this->_getCacheKey(), $infos);
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
        $info = $this->getModuleInfo($moduleName);
        return !empty($info) && $info['installed'] === true;
    }

    /**
     * Returns all currently installed AND active modules
     *
     * @return array
     */
    public function getActiveModules()
    {
        $modules = $this->getAllModules();

        foreach ($this->allModules as $key => $values) {
            if ($values['active'] === false) {
                unset($modules[$key]);
            }
        }

        return $modules;
    }

    /**
     * Returns all currently installed modules
     *
     * @return mixed
     */
    public function getInstalledModules()
    {
        $modules = $this->getAllModules();

        foreach ($this->allModules as $key => $values) {
            if ($values['installed'] === false) {
                unset($modules[$key]);
            }
        }

        return $modules;
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
            $dir = array_diff(scandir(MODULES_DIR), ['.', '..']);
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