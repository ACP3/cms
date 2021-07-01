<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Modules\ModuleInfoInterface;
use MJS\TopSort\Implementations\StringSort;

class Modules
{
    /**
     * @var ModuleInfoInterface
     */
    private $moduleInfo;
    /**
     * @var array
     */
    private $modulesInfo = [];
    /**
     * @var array
     */
    private $allModulesTopSorted = [];

    public function __construct(
        ModuleInfoInterface $moduleInfo
    ) {
        $this->moduleInfo = $moduleInfo;
    }

    /**
     * Returns, whether a module is active or not.
     *
     * @deprecated since ACP3 version 5.18.0. To be removed with version 6.0.0. Use method Modules::isInstalled() instead.
     */
    public function isActive(string $moduleName): bool
    {
        return $this->isInstalled($moduleName);
    }

    /**
     * Returns the available information about the given module.
     */
    public function getModuleInfo(string $moduleName): array
    {
        $moduleName = strtolower($moduleName);
        if (empty($this->modulesInfo)) {
            $this->modulesInfo = $this->moduleInfo->getModulesInfo();
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
     *
     * @deprecated since ACP3 version 5.18.0. To be removed with version 6.0.0. Use method Modules::getInstalledModules() instead.
     */
    public function getActiveModules(): array
    {
        return $this->getInstalledModules();
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
            $this->modulesInfo = $this->moduleInfo->getModulesInfo();
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
