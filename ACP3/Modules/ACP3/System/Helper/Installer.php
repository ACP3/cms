<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core;

class Installer
{
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Modules\SchemaInstaller
     */
    protected $schemaInstaller;
    /**
     * @var Core\Installer\SchemaRegistrar
     */
    private $schemaRegistrar;

    public function __construct(
        Core\Modules $modules,
        Core\Installer\SchemaRegistrar $schemaRegistrar,
        Core\Modules\SchemaInstaller $schemaInstaller
    ) {
        $this->modules = $modules;
        $this->schemaInstaller = $schemaInstaller;
        $this->schemaRegistrar = $schemaRegistrar;
    }

    /**
     * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls.
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     */
    public function checkInstallDependencies(Core\Modules\Installer\SchemaInterface $schema): array
    {
        $dependencies = $this->getDependencies($schema->getModuleName());
        $modulesToEnable = [];

        foreach ($dependencies as $dependency) {
            if ($this->modules->isActive($dependency)) {
                continue;
            }

            $moduleInfo = $this->modules->getModuleInfo($dependency);
            $modulesToEnable[] = $moduleInfo['name'];
        }

        return $modulesToEnable;
    }

    public function checkUninstallDependencies(Core\Modules\Installer\SchemaInterface $schema): array
    {
        $modules = $this->modules->getInstalledModules();
        $moduleDependencies = [];

        foreach ($modules as $module) {
            if ($module['name'] === $schema->getModuleName()) {
                continue;
            }

            if ($this->schemaRegistrar->has($module['name']) === false) {
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
     */
    protected function getDependencies(string $moduleName): array
    {
        return $this->modules->getModuleInfo($moduleName)['dependencies'] ?? [];
    }
}
