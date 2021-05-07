<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Modules\ModuleInfoCacheInterface;
use MJS\TopSort\Implementations\StringSort;

class Modules
{
    /**
     * @var \ACP3\Core\Modules\ModuleInfoCacheInterface
     */
    private $moduleInfoCache;
    /**
     * @var array
     */
    private $modulesInfo = [];
    /**
     * @var array
     */
    private $allModulesTopSorted = [];

    public function __construct(
        ModuleInfoCacheInterface $moduleInfoCache
    ) {
        $this->moduleInfoCache = $moduleInfoCache;
    }

    /**
     * Returns, whether a module is active or not.
     */
    public function isActive(string $moduleName): bool
    {
        $info = $this->getModuleInfo($moduleName);

        return !empty($info) && $info['active'] === true;
    }

    /**
     * Returns the available information about the given module.
     */
    public function getModuleInfo(string $moduleName): array
    {
        $moduleName = strtolower($moduleName);
        if (empty($this->modulesInfo)) {
            $this->modulesInfo = $this->moduleInfoCache->getModulesInfoCache();
        }

        return !empty($this->modulesInfo[$moduleName]) ? $this->modulesInfo[$moduleName] : [];
    }

    public function getModuleId(string $moduleName): int
    {
        $info = $this->getModuleInfo($moduleName);

        return !empty($info) ? $info['id'] : 0;
    }

    /**
     * Checks, whether a module is currently installed or not.
     */
    public function isInstalled(string $moduleName): bool
    {
        $info = $this->getModuleInfo($moduleName);

        return !empty($info) && ($info['installed'] === true || $info['installable'] === false);
    }

    /**
     * Checks whether a module is installable or not.
     */
    public function isInstallable(string $moduleName): bool
    {
        $info = $this->getModuleInfo($moduleName);

        return !empty($info) && $info['installable'] === true;
    }

    /**
     * Returns all currently installed AND active modules.
     */
    public function getActiveModules(): array
    {
        return array_filter($this->getAllModulesAlphabeticallySorted(), static function (array $module) {
            return $module['active'] === true;
        });
    }

    /**
     * Returns all currently installed modules.
     */
    public function getInstalledModules(): array
    {
        return array_filter($this->getAllModulesAlphabeticallySorted(), static function (array $module) {
            return $module['installed'] === true;
        });
    }

    /**
     * Returns an alphabetically sorted array of all found ACP3 modules.
     */
    public function getAllModulesAlphabeticallySorted(): array
    {
        $allModulesAlphabeticallySorted = $this->getAllModules();

        ksort($allModulesAlphabeticallySorted);

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
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getAllModulesTopSorted(): array
    {
        if (empty($this->allModulesTopSorted)) {
            $topSort = new StringSort();

            $modules = $this->getAllModules();

            foreach ($modules as $module) {
                $topSort->add(strtolower($module['name']), $module['dependencies']);
            }

            foreach ($topSort->sort() as $module) {
                $this->allModulesTopSorted[$module] = $modules[$module];
            }
        }

        return $this->allModulesTopSorted;
    }
}
