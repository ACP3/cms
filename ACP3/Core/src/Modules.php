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
     * @var array<string, array<string, mixed>>|null
     */
    private array|null $modulesInfo = null;
    /**
     * @var array<string, array<string, mixed>>|null
     */
    private array|null $allModulesTopSorted = null;

    public function __construct(private ModuleInfoInterface $moduleInfo)
    {
    }

    /**
     * Returns the available information about the given module.
     *
     * @return array<string, mixed>
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
     * Returns all currently installed modules.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getInstalledModules(): array
    {
        return array_filter($this->getAllModulesAlphabeticallySorted(), static fn (array $module) => $module['installed'] === true);
    }

    /**
     * Returns an alphabetically sorted array of all found ACP3 modules.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAllModulesAlphabeticallySorted(): array
    {
        $allModulesAlphabeticallySorted = $this->getAllModules();

        ksort($allModulesAlphabeticallySorted);

        return $allModulesAlphabeticallySorted;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getAllModules(): array
    {
        if ($this->modulesInfo === null) {
            $this->modulesInfo = $this->moduleInfo->getModulesInfo();
        }

        return $this->modulesInfo;
    }

    /**
     * Returns an array with all modules which is sorted topologically.
     *
     * @return array<string, array<string, mixed>>
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getAllModulesTopSorted(): array
    {
        if ($this->allModulesTopSorted === null) {
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
