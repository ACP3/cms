<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Installer\Model;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Filesystem;
use ACP3\Core\Installer\MigrationRegistrar;
use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Core\Installer\SchemaUpdater;
use ACP3\Core\Modules;

class SchemaUpdateModel
{
    use Modules\ModuleDependenciesTrait;

    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var SchemaUpdater
     */
    private $schemaUpdater;
    /**
     * @var Modules\Vendor
     */
    private $vendor;
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var SchemaRegistrar
     */
    private $schemaRegistrar;
    /**
     * @var MigrationRegistrar
     */
    private $migrationRegistrar;
    /**
     * @var array
     */
    private $results = [];

    /**
     * ModuleUpdateModel constructor.
     * @param ApplicationPath $applicationPath
     * @param SchemaRegistrar $schemaRegistrar
     * @param MigrationRegistrar $migrationRegistrar
     * @param Modules\Vendor $vendor
     * @param Modules $modules
     * @param SchemaUpdater $schemaUpdater
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SchemaRegistrar $schemaRegistrar,
        MigrationRegistrar $migrationRegistrar,
        Modules\Vendor $vendor,
        Modules $modules,
        SchemaUpdater $schemaUpdater)
    {
        $this->applicationPath = $applicationPath;
        $this->vendor = $vendor;
        $this->modules = $modules;
        $this->schemaUpdater = $schemaUpdater;
        $this->schemaRegistrar = $schemaRegistrar;
        $this->migrationRegistrar = $migrationRegistrar;
    }

    /**
     * @param array $modules
     * @return array
     */
    public function updateModules(array $modules = []): array
    {
        foreach ($this->vendor->getVendors() as $vendor) {
            $vendorPath = $this->applicationPath->getModulesDir() . $vendor . '/';
            $vendorModules = count($modules) > 0 ? $modules : Filesystem::scandir($vendorPath);

            foreach ($vendorModules as $module) {
                $module = strtolower($module);

                if (isset($this->results[$module])) {
                    continue;
                }

                $moduleConfigPath = $vendorPath . ucfirst($module) . '/composer.json';
                if (is_file($moduleConfigPath)) {
                    $dependencies = $this->getModuleDependencies($moduleConfigPath);

                    if (count($dependencies) > 0) {
                        $this->updateModules($dependencies);
                    }

                    $this->results[$module] = $this->updateModule($module);
                }
            }
        }

        return $this->results;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus
     *
     * @param string $moduleName
     * @return int
     */
    private function updateModule(string $moduleName)
    {
        $result = false;

        $serviceIdMigration = $moduleName . '.installer.migration';
        if ($this->schemaRegistrar->has($moduleName) === true &&
            $this->migrationRegistrar->has($serviceIdMigration) === true
        ) {
            $moduleSchema = $this->schemaRegistrar->get($moduleName);
            $moduleMigration = $this->migrationRegistrar->get($serviceIdMigration);
            if ($this->modules->isInstalled($moduleName) || count($moduleMigration->renameModule()) > 0) {
                $result = $this->schemaUpdater->updateSchema($moduleSchema, $moduleMigration);
            }
        }

        return $result;
    }
}
