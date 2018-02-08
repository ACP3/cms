<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules\Helper\ControllerActionExists;
use ACP3\Core\Modules\ModuleInfoCache;
use ACP3\Core\Modules\Vendor;
use MJS\TopSort\Implementations\StringSort;

class Modules
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Modules\Helper\ControllerActionExists
     */
    protected $controllerActionExists;
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
     * @param \ACP3\Core\Environment\ApplicationPath           $appPath
     * @param \ACP3\Core\Modules\Helper\ControllerActionExists $controllerActionExists
     * @param \ACP3\Core\Modules\ModuleInfoCache               $moduleInfoCache
     * @param \ACP3\Core\Modules\Vendor                        $vendors
     */
    public function __construct(
        ApplicationPath $appPath,
        ControllerActionExists $controllerActionExists,
        ModuleInfoCache $moduleInfoCache,
        Vendor $vendors
    ) {
        $this->appPath = $appPath;
        $this->controllerActionExists = $controllerActionExists;
        $this->moduleInfoCache = $moduleInfoCache;
        $this->vendors = $vendors;
    }

    /**
     * Returns, whether the given module controller action exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function controllerActionExists($path)
    {
        return $this->controllerActionExists->controllerActionExists($path);
    }

    /**
     * Returns, whether a module is active or not.
     *
     * @param string $module
     *
     * @return bool
     */
    public function isActive($module)
    {
        $info = $this->getModuleInfo($module);

        return !empty($info) && $info['active'] === true;
    }

    /**
     * Returns the available information about the given module.
     *
     * @param string $module
     *
     * @return array
     */
    public function getModuleInfo($module)
    {
        $module = \strtolower($module);
        if (empty($this->modulesInfo)) {
            $this->modulesInfo = $this->moduleInfoCache->getModulesInfoCache();
        }

        return !empty($this->modulesInfo[$module]) ? $this->modulesInfo[$module] : [];
    }

    /**
     * @param string $module
     *
     * @return int
     */
    public function getModuleId($module)
    {
        $info = $this->getModuleInfo($module);

        return !empty($info) ? $info['id'] : 0;
    }

    /**
     * Checks, whether a module is currently installed or not.
     *
     * @param string $moduleName
     *
     * @return bool
     */
    public function isInstalled($moduleName)
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
