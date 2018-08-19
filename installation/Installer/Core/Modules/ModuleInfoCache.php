<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Modules;


use ACP3\Core\Filesystem;
use ACP3\Core\Modules\ModuleDependenciesTrait;
use ACP3\Core\Modules\ModuleInfoCacheInterface;
use ACP3\Core\Modules\Vendor;
use ACP3\Core\XML;
use ACP3\Installer\Core\Environment\ApplicationPath;

class ModuleInfoCache implements ModuleInfoCacheInterface
{
    use ModuleDependenciesTrait;

    /**
     * @var \ACP3\Core\XML
     */
    private $xml;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    private $vendors;
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    private $appPath;

    /**
     * @var array
     */
    private $moduleInfoCache = [];

    public function __construct(ApplicationPath $appPath, Vendor $vendors, XML $xml)
    {
        $this->xml = $xml;
        $this->vendors = $vendors;
        $this->appPath = $appPath;
    }

    /**
     * @return array
     */
    public function getModulesInfoCache(): array
    {
        if (empty($this->moduleInfoCache)) {
            $this->saveModulesInfoCache();
        }

        return $this->moduleInfoCache;
    }

    /**
     * Saves the modules info cache.
     */
    public function saveModulesInfoCache(): void
    {
        $infos = [];

        // 1. fetch all core modules
        // 2. Fetch all 3rd party modules
        foreach ($this->vendors->getVendors() as $vendor) {
            $infos += $this->fetchVendorModules($vendor);
        }

        $this->moduleInfoCache = $infos;
    }

    /**
     * @param string $vendor
     *
     * @return array
     */
    protected function fetchVendorModules(string $vendor): array
    {
        $infos = [];

        foreach (Filesystem::scandir($this->appPath->getModulesDir() . $vendor . '/') as $module) {
            $moduleInfo = $this->fetchModuleInfo($vendor, $module);

            if (!empty($moduleInfo)) {
                $infos[$moduleInfo['name']] = $moduleInfo;
            }
        }

        return $infos;
    }

    /**
     * @param string $vendor
     * @param string $moduleDirectory
     *
     * @return array
     */
    protected function fetchModuleInfo(string $vendor, string $moduleDirectory): array
    {
        $path = $this->appPath->getModulesDir() . $vendor . '/' . $moduleDirectory . '/Resources/config/module.xml';
        if (\is_file($path) === false) {
            return [];
        }

        $moduleInfo = $this->xml->parseXmlFile($path, 'info');

        if (empty($moduleInfo) === true) {
            return [];
        }

        $moduleName = \strtolower($moduleDirectory);

        return [
            'vendor' => $vendor,
            'id' => 0,
            'dir' => $moduleDirectory,
            'installed' => false,
            'active' => false,
            'schema_version' => 0,
            'author' => $moduleInfo['author'],
            'version' => $moduleInfo['version'],
            'name' => $moduleName,
            'categories' => isset($moduleInfo['categories']),
            'protected' => isset($moduleInfo['protected']),
            'installable' => !isset($moduleInfo['no_install']),
            'dependencies' => $this->getModuleDependencies($path),
        ];
    }

    /**
     * @return XML
     */
    protected function getXml()
    {
        return $this->xml;
    }
}
