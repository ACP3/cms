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
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
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
     * @param string $moduleName
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function updateModule(string $moduleName)
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
