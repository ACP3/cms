<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer\Model;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Installer\MigrationRegistrar;
use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Core\Installer\SchemaUpdater;
use ACP3\Core\Modules;

class SchemaUpdateModel
{
    use Modules\ModuleDependenciesTrait;

    /**
     * @var Modules\Modules
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
     * @param Modules\Modules $modules
     * @param SchemaUpdater $schemaUpdater
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SchemaRegistrar $schemaRegistrar,
        MigrationRegistrar $migrationRegistrar,
        Modules\Vendor $vendor,
        Modules\Modules $modules,
        SchemaUpdater $schemaUpdater
    ) {
        $this->applicationPath = $applicationPath;
        $this->vendor = $vendor;
        $this->modules = $modules;
        $this->schemaUpdater = $schemaUpdater;
        $this->schemaRegistrar = $schemaRegistrar;
        $this->migrationRegistrar = $migrationRegistrar;
    }

    /**
     * @return array
     */
    public function updateModules(): array
    {
        foreach ($this->modules->getAllModulesTopSorted() as $moduleInfo) {
            $module = \strtolower($moduleInfo['dir']);
            $this->results[$module] = $this->updateModule($module);
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
            if ($this->modules->isInstalled($moduleName) || \count($moduleMigration->renameModule()) > 0) {
                $result = $this->schemaUpdater->updateSchema($moduleSchema, $moduleMigration);
            }
        }

        return $result;
    }
}
