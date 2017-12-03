<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Filesystem;
use ACP3\Modules\ACP3\System;
use MJS\TopSort\Implementations\StringSort;

/**
 * Class Modules
 * @package ACP3\Core
 */
class Modules
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Modules\ModuleInfoCache
     */
    protected $moduleInfoCache;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    protected $vendors;
    /**
     * @var array
     */
    private $modulesInfo = [];
    /**
     * @var array
     */
    private $allModules = [];

    /**
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Modules\ModuleInfoCache $moduleInfoCache
     * @param \ACP3\Core\Modules\Vendor $vendors
     */
    public function __construct(
        ApplicationPath $appPath,
        ModuleInfoCache $moduleInfoCache,
        Vendor $vendors
    ) {
        $this->appPath = $appPath;
        $this->moduleInfoCache = $moduleInfoCache;
        $this->vendors = $vendors;
    }

    /**
     * Returns, whether a module is active or not
     *
     * @param string $moduleName
     *
     * @return boolean
     */
    public function isActive(string $moduleName): bool
    {
        $info = $this->getModuleInfo($moduleName);
        return !empty($info) && $info['active'] === true;
    }

    /**
     * Returns the available information about the given module
     *
     * @param string $moduleName
     *
     * @return array
     */
    public function getModuleInfo(string $moduleName): array
    {
        $moduleName = strtolower($moduleName);
        if (empty($this->modulesInfo)) {
            $this->modulesInfo = $this->moduleInfoCache->getModulesInfoCache();
        }
        return !empty($this->modulesInfo[$moduleName]) ? $this->modulesInfo[$moduleName] : [];
    }

    /**
     * @param string $moduleName
     *
     * @return integer
     */
    public function getModuleId(string $moduleName): int
    {
        $info = $this->getModuleInfo($moduleName);
        return $info['id'] ?? 0;
    }

    /**
     * Checks, whether a module is currently installed or not
     *
     * @param string $moduleName
     *
     * @return boolean
     */
    public function isInstalled(string $moduleName): bool
    {
        $info = $this->getModuleInfo($moduleName);
        return !empty($info) && $info['installed'] === true || $info['installable'] === false;
    }

    /**
     * Returns all currently installed AND active modules
     *
     * @return array
     */
    public function getActiveModules(): array
    {
        $modules = $this->getAllModulesAlphabeticallySorted();

        foreach ($modules as $key => $values) {
            if ($values['active'] === false) {
                unset($modules[$key]);
            }
        }

        return $modules;
    }

    /**
     * Returns all currently installed modules
     *
     * @return array
     */
    public function getInstalledModules(): array
    {
        $modules = $this->getAllModulesAlphabeticallySorted();

        foreach ($modules as $key => $values) {
            if ($values['installed'] === false) {
                unset($modules[$key]);
            }
        }

        return $modules;
    }

    /**
     * Returns an alphabetically sorted array of all found ACP3 modules
     *
     * @return array
     */
    public function getAllModulesAlphabeticallySorted(): array
    {
        $allModulesAlphabeticallySorted = [];
        foreach ($this->getAllModules() as $info) {
            $allModulesAlphabeticallySorted[$info['name']] = $info;
        }

        ksort($allModulesAlphabeticallySorted);

        return $allModulesAlphabeticallySorted;
    }

    private function getAllModules(): array
    {
        if (empty($this->allModules)) {
            foreach ($this->vendors->getVendors() as $vendor) {
                foreach (Filesystem::scandir($this->appPath->getModulesDir() . $vendor . '/') as $module) {
                    $info = $this->getModuleInfo($module);
                    if (!empty($info)) {
                        $info['vendor'] = $vendor;
                        $this->allModules[strtolower($module)] = $info;
                    }
                }
            }
        }

        return $this->allModules;
    }

    /**
     * Returns an array with all modules which is sorted topologically
     *
     * @return array
     */
    public function getAllModulesTopSorted(): array
    {
        $topSort = new StringSort();

        $modules = $this->getAllModules();
        foreach ($modules as $module) {
            $topSort->add(strtolower($module['dir']), $module['dependencies']);
        }

        $topSortedModules = [];
        foreach ($topSort->sort() as $module) {
            $topSortedModules[$module] = $modules[$module];
        }

        return $topSortedModules;
    }
}
