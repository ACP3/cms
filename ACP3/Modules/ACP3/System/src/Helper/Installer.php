<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core;

class Installer
{
    public function __construct(private readonly Core\Modules $modules)
    {
    }

    /**
     * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls.
     *
     * @return string[]
     */
    public function checkInstallDependencies(Core\Installer\SchemaInterface $schema): array
    {
        $dependencies = $this->getDependencies($schema->getModuleName());
        $modulesToEnable = [];

        foreach ($dependencies as $dependency) {
            if ($this->modules->isInstalled($dependency)) {
                continue;
            }

            $moduleInfo = $this->modules->getModuleInfo($dependency);
            $modulesToEnable[] = $moduleInfo['name'];
        }

        return $modulesToEnable;
    }

    /**
     * @return string[]
     */
    public function checkUninstallDependencies(Core\Installer\SchemaInterface $schema): array
    {
        $modules = $this->modules->getInstalledModules();
        $moduleDependencies = [];

        foreach ($modules as $module) {
            if ($module['name'] === $schema->getModuleName()) {
                continue;
            }

            $dependencies = $this->getDependencies($module['name']);

            if (\in_array($schema->getModuleName(), $dependencies, true) === true) {
                $moduleDependencies[] = $module['name'];
            }
        }

        return $moduleDependencies;
    }

    /**
     * Gibt ein Array mit den Abhängigkeiten zu anderen Modulen eines Moduls zurück.
     *
     * @return string[]
     */
    private function getDependencies(string $moduleName): array
    {
        return $this->modules->getModuleInfo($moduleName)['dependencies'] ?? [];
    }
}
