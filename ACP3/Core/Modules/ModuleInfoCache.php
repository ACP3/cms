<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Cache;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Filesystem;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use ACP3\Core\XML;

class ModuleInfoCache
{
    use ModuleDependenciesTrait;

    /**
     * @var \ACP3\Core\Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    protected $vendors;
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var ModuleAwareRepositoryInterface
     */
    protected $systemModuleRepository;

    /**
     * ModuleInfoCache constructor.
     *
     * @param Cache                          $cache
     * @param ApplicationPath                $appPath
     * @param Vendor                         $vendors
     * @param XML                            $xml
     * @param ModuleAwareRepositoryInterface $systemModuleRepository
     */
    public function __construct(
        Cache $cache,
        ApplicationPath $appPath,
        Vendor $vendors,
        XML $xml,
        ModuleAwareRepositoryInterface $systemModuleRepository
    ) {
        $this->cache = $cache;
        $this->appPath = $appPath;
        $this->vendors = $vendors;
        $this->xml = $xml;
        $this->systemModuleRepository = $systemModuleRepository;
    }

    /**
     * @return string
     */
    public function getCacheKey(): string
    {
        return 'modules_info';
    }

    /**
     * @return array
     */
    public function getModulesInfoCache(): array
    {
        if ($this->cache->contains($this->getCacheKey()) === false) {
            $this->saveModulesInfoCache();
        }

        return $this->cache->fetch($this->getCacheKey());
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

        $this->cache->save($this->getCacheKey(), $infos);
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
        $moduleInfoDb = $this->systemModuleRepository->getInfoByModuleName($moduleName);

        return [
            'vendor' => $vendor,
            'id' => !empty($moduleInfoDb) ? $moduleInfoDb['id'] : 0,
            'dir' => $moduleDirectory,
            'installed' => (!empty($moduleInfoDb)),
            'active' => (!empty($moduleInfoDb) && $moduleInfoDb['active'] == 1),
            'schema_version' => !empty($moduleInfoDb) ? (int) $moduleInfoDb['version'] : 0,
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
