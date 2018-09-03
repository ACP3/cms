<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer\Model;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Installer\MigrationRegistrar;
use ACP3\Core\Installer\SchemaRegistrar;
use ACP3\Core\Modules;
use ACP3\Core\Modules\SchemaUpdater;
use ACP3\Core\XML;

class SchemaUpdateModel
{
    use Modules\ModuleDependenciesTrait;

    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var SchemaUpdater
     */
    private $schemaUpdater;
    /**
     * @var \ACP3\Core\Modules\Vendor
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
     * @var \ACP3\Core\XML
     */
    private $xml;
    /**
     * @var array
     */
    private $results = [];

    /**
     * SchemaUpdateModel constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath  $applicationPath
     * @param \ACP3\Core\Installer\SchemaRegistrar    $schemaRegistrar
     * @param \ACP3\Core\Installer\MigrationRegistrar $migrationRegistrar
     * @param \ACP3\Core\Modules\Vendor               $vendor
     * @param \ACP3\Core\Modules                      $modules
     * @param \ACP3\Core\Modules\SchemaUpdater        $schemaUpdater
     * @param \ACP3\Core\XML                          $xml
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SchemaRegistrar $schemaRegistrar,
        MigrationRegistrar $migrationRegistrar,
        Modules\Vendor $vendor,
        Modules $modules,
        SchemaUpdater $schemaUpdater,
        XML $xml
    ) {
        $this->applicationPath = $applicationPath;
        $this->vendor = $vendor;
        $this->modules = $modules;
        $this->schemaUpdater = $schemaUpdater;
        $this->schemaRegistrar = $schemaRegistrar;
        $this->migrationRegistrar = $migrationRegistrar;
        $this->xml = $xml;
    }

    /**
     * @return array
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     * @throws \Doctrine\DBAL\ConnectionException
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
     * Führt die Updateanweisungen eines Moduls aus.
     *
     * @param string $moduleName
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\ConnectionException
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

    /**
     * @return XML
     */
    protected function getXml()
    {
        return $this->xml;
    }
}
