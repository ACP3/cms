<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Modules\Update\Model;

use ACP3\Core\Installer\MigrationRegistrar;
use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Core\Modules;

class SchemaUpdateModel
{
    /**
     * @var Modules
     */
    protected $modules;
    /**
     * @var Modules\SchemaUpdater
     */
    protected $schemaUpdater;
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
    protected $results = [];

    /**
     * ModuleUpdateModel constructor.
     *
     * @param SchemaRegistrar       $schemaRegistrar
     * @param MigrationRegistrar    $migrationRegistrar
     * @param Modules               $modules
     * @param Modules\SchemaUpdater $schemaUpdater
     */
    public function __construct(
        SchemaRegistrar $schemaRegistrar,
        MigrationRegistrar $migrationRegistrar,
        Modules $modules,
        Modules\SchemaUpdater $schemaUpdater
    ) {
        $this->modules = $modules;
        $this->schemaUpdater = $schemaUpdater;
        $this->schemaRegistrar = $schemaRegistrar;
        $this->migrationRegistrar = $migrationRegistrar;
    }

    /**
     * @return array
     */
    public function updateModules()
    {
        foreach ($this->modules->getAllModulesTopSorted() as $moduleInfo) {
            $module = \strtolower($moduleInfo['dir']);
            $this->results[$module] = $this->updateModule($module);
        }

        return $this->results;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus.
     *
     * @param string $module
     *
     * @return int
     */
    public function updateModule(string $module)
    {
        $result = false;

        $serviceIdMigration = $module . '.installer.migration';
        if ($this->schemaRegistrar->has($module) === true &&
            $this->migrationRegistrar->has($serviceIdMigration) === true
        ) {
            $moduleSchema = $this->schemaRegistrar->get($module);
            $moduleMigration = $this->migrationRegistrar->get($serviceIdMigration);
            if ($this->modules->isInstalled($module) || \count($moduleMigration->renameModule()) > 0) {
                $result = $this->schemaUpdater->updateSchema($moduleSchema, $moduleMigration);
            }
        }

        return $result;
    }
}
