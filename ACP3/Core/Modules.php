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
    private $allModulesTopSorted = [];

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
    public function controllerActionExists(string $path): bool
    {
        return $this->controllerActionExists->controllerActionExists($path);
    }

    /**
     * Returns, whether a module is active or not.
     *
     * @param string $moduleName
     *
     * @return bool
     */
    public function isActive(string $moduleName): bool
    {
        $info = $this->getModuleInfo($moduleName);

        return !empty($info) && $info['active'] === true;
    }

    /**
     * Returns the available information about the given module.
     *
     * @param string $moduleName
     *
     * @return array
     */
    public function getModuleInfo(string $moduleName): array
    {
        $moduleName = \strtolower($moduleName);
        if (empty($this->modulesInfo)) {
            $this->modulesInfo = $this->moduleInfoCache->getModulesInfoCache();
        }

        return !empty($this->modulesInfo[$moduleName]) ? $this->modulesInfo[$moduleName] : [];
    }

    /**
     * @param string $moduleName
     *
     * @return int
     */
    public function getModuleId(string $moduleName): int
    {
        $info = $this->getModuleInfo($moduleName);

        return !empty($info) ? $info['id'] : 0;
    }

    /**
     * Checks, whether a module is currently installed or not.
     *
     * @param string $moduleName
     *
     * @return bool
     */
    public function isInstalled(string $moduleName): bool
    {
        $info = $this->getModuleInfo($moduleName);

        return !empty($info) && $info['installed'] === true || $info['installable'] === false;
    }

    /**
     * Returns all currently installed AND active modules.
     *
     * @return array
     */
    public function getActiveModules(): array
    {
        return \array_filter($this->getAllModulesAlphabeticallySorted(), function (array $module) {
            return $module['active'] === true;
        });
    }

    /**
     * Returns all currently installed modules.
     *
     * @return array
     */
    public function getInstalledModules(): array
    {
        return \array_filter($this->getAllModulesAlphabeticallySorted(), function (array $module) {
            return $module['installed'] === true;
        });
    }

    /**
     * Returns an alphabetically sorted array of all found ACP3 modules.
     *
     * @return array
     */
    public function getAllModulesAlphabeticallySorted(): array
    {
        $allModulesAlphabeticallySorted = $this->getAllModules();

        \ksort($allModulesAlphabeticallySorted);

        return $allModulesAlphabeticallySorted;
    }

    private function getAllModules(): array
    {
        if (empty($this->modulesInfo)) {
            $this->modulesInfo = $this->moduleInfoCache->getModulesInfoCache();
        }

        return $this->modulesInfo;
    }

    /**
     * Returns an array with all modules which is sorted topologically.
     *
     * @return array
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getAllModulesTopSorted(): array
    {
        if (empty($this->allModulesTopSorted)) {
            $topSort = new StringSort();

            $modules = $this->getAllModules();
            foreach ($modules as $module) {
                $topSort->add(\strtolower($module['dir']), $module['dependencies']);
            }

            foreach ($topSort->sort() as $module) {
                $this->allModulesTopSorted[$module] = $modules[$module];
            }
        }

        return $this->allModulesTopSorted;
    }
}
