<?php
namespace ACP3\Core\Modules;

use ACP3\Core\Cache;
use ACP3\Core\Filesystem;
use ACP3\Core\Lang;
use ACP3\Core\XML;
use ACP3\Modules\ACP3\System\Model\ModuleRepository;

/**
 * Class ModuleInfoCache
 * @package ACP3\Core\Modules
 */
class ModuleInfoCache
{
    /**
     * @var \ACP3\Core\Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Modules\Vendors
     */
    protected $vendors;
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\ModuleRepository
     */
    protected $systemModuleRepository;

    /**
     * @param \ACP3\Core\Cache                                 $cache
     * @param \ACP3\Core\Lang                                  $lang
     * @param \ACP3\Core\Modules\Vendors                       $vendors
     * @param \ACP3\Core\XML                                   $xml
     * @param \ACP3\Modules\ACP3\System\Model\ModuleRepository $systemModuleRepository
     */
    public function __construct(
        Cache $cache,
        Lang $lang,
        Vendors $vendors,
        XML $xml,
        ModuleRepository $systemModuleRepository
    )
    {
        $this->cache = $cache;
        $this->lang = $lang;
        $this->vendors = $vendors;
        $this->xml = $xml;
        $this->systemModuleRepository = $systemModuleRepository;
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return 'infos_' . $this->lang->getLanguage();
    }

    /**
     * @return array
     */
    public function getModulesInfoCache()
    {
        if ($this->cache->contains($this->getCacheKey()) === false) {
            $this->saveModulesInfoCache();
        }

        return $this->cache->fetch($this->getCacheKey());
    }

    /**
     * Saves the modules info cache
     */
    public function saveModulesInfoCache()
    {
        $infos = [];

        // 1. fetch all core modules
        // 2. Fetch all 3rd party modules
        // 3. Fetch all local module customizations
        foreach ($this->vendors->getVendors() as $vendor) {
            $infos += $this->_fetchVendorModules($vendor);
        }

        $this->cache->save($this->getCacheKey(), $infos);
    }

    /**
     * @param string $vendor
     *
     * @return array
     */
    protected function _fetchVendorModules($vendor)
    {
        $infos = [];

        $modules = Filesystem::scandir(MODULES_DIR . $vendor . '/');

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
     * @param string $moduleDirectory
     *
     * @return array
     */
    protected function _fetchModuleInfo($moduleDirectory)
    {
        $vendors = array_reverse($this->vendors->getVendors()); // Reverse the order of the array -> search module customizations first, then 3rd party modules, then core modules
        foreach ($vendors as $vendor) {
            $path = MODULES_DIR . $vendor . '/' . $moduleDirectory . '/config/module.xml';
            if (is_file($path) === true) {
                $moduleInfo = $this->xml->parseXmlFile($path, 'info');

                if (!empty($moduleInfo)) {
                    $moduleName = strtolower($moduleDirectory);
                    $moduleInfoDb = $this->systemModuleRepository->getInfoByModuleName($moduleName);

                    return [
                        'id' => !empty($moduleInfoDb) ? $moduleInfoDb['id'] : 0,
                        'dir' => $moduleDirectory,
                        'installed' => (!empty($moduleInfoDb)),
                        'active' => (!empty($moduleInfoDb) && $moduleInfoDb['active'] == 1),
                        'schema_version' => !empty($moduleInfoDb) ? (int)$moduleInfoDb['version'] : 0,
                        'description' => $this->getModuleDescription($moduleInfo, $moduleName),
                        'author' => $moduleInfo['author'],
                        'version' => $moduleInfo['version'],
                        'name' => $this->getModuleName($moduleInfo, $moduleName),
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
     * @param array  $moduleInfo
     * @param string $moduleName
     *
     * @return string
     */
    protected function getModuleDescription(array $moduleInfo, $moduleName)
    {
        if (isset($moduleInfo['description']['lang']) && $moduleInfo['description']['lang'] === 'true') {
            return $this->lang->t($moduleName, 'mod_description');
        }

        return $moduleInfo['description'];
    }

    /**
     * @param array  $moduleInfo
     * @param string $moduleName
     *
     * @return string
     */
    protected function getModuleName(array $moduleInfo, $moduleName)
    {
        if (isset($moduleInfo['name']['lang']) && $moduleInfo['name']['lang'] === 'true') {
            return $this->lang->t($moduleName, $moduleName);
        }

        return $moduleInfo['name'];
    }

}