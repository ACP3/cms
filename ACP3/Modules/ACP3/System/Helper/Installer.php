<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core;
use Psr\Log\LoggerInterface;

class Installer
{
    use Core\Modules\ModuleDependenciesTrait;

    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Modules\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Installer\SchemaInstaller
     */
    protected $schemaInstaller;
    /**
     * @var \ACP3\Core\Modules\Vendor
     */
    protected $vendors;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Core\Installer\SchemaRegistrar
     */
    private $schemaRegistrar;

    /**
     * @param LoggerInterface $logger
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Modules\Modules $modules
     * @param \ACP3\Core\Modules\Vendor $vendors
     * @param Core\Installer\SchemaRegistrar $schemaRegistrar
     * @param \ACP3\Core\Installer\SchemaInstaller $schemaInstaller
     */
    public function __construct(
        LoggerInterface $logger,
        Core\Environment\ApplicationPath $appPath,
        Core\Modules\Modules $modules,
        Core\Modules\Vendor $vendors,
        Core\Installer\SchemaRegistrar $schemaRegistrar,
        Core\Installer\SchemaInstaller $schemaInstaller
    ) {
        $this->appPath = $appPath;
        $this->modules = $modules;
        $this->vendors = $vendors;
        $this->schemaInstaller = $schemaInstaller;
        $this->logger = $logger;
        $this->schemaRegistrar = $schemaRegistrar;
    }

    /**
     * Überprüft die Modulabhängigkeiten beim Installieren eines Moduls
     *
     * @param \ACP3\Core\Installer\SchemaInterface $schema
     *
     * @return array
     */
    public function checkInstallDependencies(Core\Installer\SchemaInterface $schema)
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
     * @param \ACP3\Core\Installer\SchemaInterface $schema
     * @return array
     */
    public function checkUninstallDependencies(Core\Installer\SchemaInterface $schema)
    {
        $modules = $this->modules->getInstalledModules();
        $moduleDependencies = [];

        foreach ($modules as $module) {
            $moduleName = strtolower($module['dir']);
            if ($moduleName !== $schema->getModuleName()) {
                if ($this->schemaRegistrar->has($moduleName) === true) {
                    $dependencies = $this->getDependencies($moduleName);

                    if (in_array($schema->getModuleName(), $dependencies) === true) {
                        $moduleDependencies[] = $module['name'];
                    }
                }
            }
        }
        return $moduleDependencies;
    }

    /**
     * Gibt ein Array mit den Abhängigkeiten zu anderen Modulen eines Moduls zurück
     *
     * @param string $moduleName
     *
     * @return array
     */
    protected function getDependencies(string $moduleName): array
    {
        if ((bool)preg_match('=/=', $moduleName) === false) {
            foreach ($this->vendors->getVendors() as $vendor) {
                $path = $this->appPath->getModulesDir() . $vendor . '/' . ucfirst($moduleName) . '/composer.json';

                if (is_file($path) === true) {
                    return $this->getModuleDependencies($path);
                }
            }
        }

        return [];
    }
}
