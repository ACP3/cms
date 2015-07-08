<?php
namespace ACP3\Core;

use ACP3\Modules\ACP3\System;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Modules
 * @package ACP3\Core
 */
class Modules
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
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
     * @var \ACP3\Modules\ACP3\System\Model
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
     * @var array
     */
    private $moduleNamespaces = [];

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \ACP3\Core\Lang                                           $lang
     * @param \ACP3\Core\XML                                            $xml
     * @param \ACP3\Core\Cache                                          $modulesCache
     * @param \ACP3\Modules\ACP3\System\Model                           $systemModel
     */
    public function __construct(
        ContainerInterface $container,
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
        $info = $this->getModuleInfo($module);
        return !empty($info) ? $info['id'] : 0;
    }

    /**
     * @return string
     */
    protected function _getCacheKey()
    {
        return 'infos_' . $this->lang->getLanguage();
    }

    /**
     * @return array
     */
    public function getModuleNamespaces()
    {
        if ($this->moduleNamespaces === []) {
            $this->moduleNamespaces = array_merge(
                ['ACP3'],
                array_diff(scandir(MODULES_DIR), ['.', '..', 'ACP3', 'Custom']),
                ['Custom']
            );
        }

        return $this->moduleNamespaces;
    }

    /**
     * Setzt den Cache für alle vorliegenden Modulinformationen
     */
    public function setModulesCache()
    {
        $infos = [];

        // 1. fetch all core modules
        // 2. Fetch all 3rd party modules
        // 3. Fetch all local module customizations
        foreach ($this->getModuleNamespaces() as $namespace) {
            $infos += $this->_fetchModulesInNamespaces($namespace);
        }

        $this->modulesCache->save($this->_getCacheKey(), $infos);
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    protected function _fetchModulesInNamespaces($namespace)
    {
        $infos = [];

        $modules = array_diff(scandir(MODULES_DIR . $namespace . '/'), ['.', '..', '.gitignore', '.svn', '.htaccess', '.htpasswd']);

        if (!empty($modules)) {
            foreach ($modules as $module) {
                $moduleInfo = $this->_fetchModuleInfo($module);

                if (!empty($moduleInfo)) {
                    $infos[strtolower($module)] = $moduleInfo;
                }
            }
        }

        return $infos;
    }

    /**
     * @param $moduleDirectory
     *
     * @return array
     */
    protected function _fetchModuleInfo($moduleDirectory)
    {
        $namespaces = array_reverse($this->getModuleNamespaces()); // Reverse the order of the array -> search module customizations first, then 3rd party modules, then core modules
        foreach ($namespaces as $namespace) {
            $path = MODULES_DIR . $namespace . '/' . $moduleDirectory . '/config/module.xml';
            if (is_file($path) === true) {
                $moduleInfo = $this->xml->parseXmlFile($path, 'info');

                if (!empty($moduleInfo)) {
                    $moduleName = strtolower($moduleDirectory);
                    $moduleInfoDb = $this->systemModel->getInfoByModuleName($moduleName);

                    return [
                        'id' => !empty($moduleInfoDb) ? $moduleInfoDb['id'] : 0,
                        'dir' => $moduleDirectory,
                        'installed' => (!empty($moduleInfoDb)),
                        'active' => (!empty($moduleInfoDb) && $moduleInfoDb['active'] == 1),
                        'schema_version' => !empty($moduleInfoDb) ? (int)$moduleInfoDb['version'] : 0,
                        'description' => isset($moduleInfo['description']['lang']) && $moduleInfo['description']['lang'] === 'true' ? $this->lang->t($moduleName, 'mod_description') : $moduleInfo['description']['lang'],
                        'author' => $moduleInfo['author'],
                        'version' => $moduleInfo['version'],
                        'name' => isset($moduleInfo['name']['lang']) && $moduleInfo['name']['lang'] == 'true' ? $this->lang->t($moduleName, $moduleName) : $moduleInfo['name'],
                        'categories' => isset($moduleInfo['categories']),
                        'protected' => isset($moduleInfo['protected']),
                        'dependencies' => array_values($this->xml->parseXmlFile($path, 'info/dependencies')),
                    ];
                }
            }
        }

        return [];
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
            foreach ($this->getModuleNamespaces() as $namespace) {
                $modules = array_diff(scandir(MODULES_DIR . $namespace . '/'), ['.', '..', '.gitignore', '.svn', '.htaccess', '.htpasswd']);
                foreach ($modules as $module) {
                    $info = $this->getModuleInfo($module);
                    if (!empty($info)) {
                        $this->allModules[$info['name']] = $info;
                    }
                }
            }

            ksort($this->allModules);
        }

        return $this->allModules;
    }
}