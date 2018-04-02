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
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
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

    /**
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Modules                     $modules
     * @param Core\Installer\SchemaRegistrar         $schemaRegistrar
     * @param \ACP3\Core\Modules\SchemaInstaller     $schemaInstaller
     */
    public function __construct(
        Core\Environment\ApplicationPath $appPath,
        Core\Modules $modules,
        Core\Installer\SchemaRegistrar $schemaRegistrar,
        Core\Modules\SchemaInstaller $schemaInstaller
    ) {
        $this->appPath = $appPath;
        $this->modules = $modules;
        $this->schemaInstaller = $schemaInstaller;
        $this->schemaRegistrar = $schemaRegistrar;
    }

    /**
     * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls.
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     *
     * @return array
     */
    public function checkInstallDependencies(Core\Modules\Installer\SchemaInterface $schema)
    {
        $dependencies = $this->getDependencies($schema->getModuleName());
        $modulesToEnable = [];
        if (!empty($dependencies)) {
            foreach ($dependencies as $dependency) {
                if ($this->modules->isActive($dependency) === false) {
                    $moduleInfo = $this->modules->getModuleInfo($dependency);
                    $modulesToEnable[] = $moduleInfo['name'];
                }
            }
        }

        return $modulesToEnable;
    }

    /**
     * @param Core\Modules\Installer\SchemaInterface $schema
     *
     * @return array
     */
    public function checkUninstallDependencies(Core\Modules\Installer\SchemaInterface $schema)
    {
        $modules = $this->modules->getInstalledModules();
        $moduleDependencies = [];

        foreach ($modules as $module) {
            $moduleName = \strtolower($module['dir']);
            if ($moduleName !== $schema->getModuleName()) {
                if ($this->schemaRegistrar->has($moduleName) === true) {
                    $dependencies = $this->getDependencies($moduleName);

                    if (\in_array($schema->getModuleName(), $dependencies) === true) {
                        $moduleDependencies[] = $module['name'];
                    }
                }
            }
        }

        return $moduleDependencies;
    }

    /**
     * Gibt ein Array mit den Abhängigkeiten zu anderen Modulen eines Moduls zurück.
     *
     * @param string $moduleName
     *
     * @return array
     */
    protected function getDependencies(string $moduleName)
    {
        return $this->modules->getModuleInfo($moduleName)['dependencies'] ?? [];
    }
}
