<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer\Model;

use ACP3\Core\Installer\Exception\MissingInstallerException;
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
     * @param \ACP3\Core\Installer\SchemaRegistrar    $schemaRegistrar
     * @param \ACP3\Core\Installer\MigrationRegistrar $migrationRegistrar
     * @param \ACP3\Core\Modules                      $modules
     * @param \ACP3\Core\Modules\SchemaUpdater        $schemaUpdater
     * @param \ACP3\Core\XML                          $xml
     */
    public function __construct(
        SchemaRegistrar $schemaRegistrar,
        MigrationRegistrar $migrationRegistrar,
        Modules $modules,
        SchemaUpdater $schemaUpdater,
        XML $xml
    ) {
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
     */
    public function updateModules(): array
    {
        foreach ($this->modules->getAllModulesTopSorted() as $moduleInfo) {
            $module = \strtolower($moduleInfo['dir']);

            try {
                $this->updateModule($module);

                $this->results[$module] = true;
            } catch (\Throwable $e) {
                $this->results[$module] = false;
            }
        }

        return $this->results;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus.
     *
     * @param string $moduleName
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Installer\Exception\MissingInstallerException
     * @throws \ACP3\Core\Modules\Exception\ModuleMigrationException
     */
    private function updateModule(string $moduleName): void
    {
        if (!$this->modules->isInstallable($moduleName)) {
            return;
        }

        $serviceIdMigration = $moduleName . '.installer.migration';
        if (!$this->schemaRegistrar->has($moduleName) || !$this->migrationRegistrar->has($serviceIdMigration)) {
            throw new MissingInstallerException(
                \sprintf('Could not find any schema or migration files for module "%s"', $moduleName)
            );
        }

        $moduleSchema = $this->schemaRegistrar->get($moduleName);
        $moduleMigration = $this->migrationRegistrar->get($serviceIdMigration);
        if ($this->modules->isInstalled($moduleName) || \count($moduleMigration->renameModule()) > 0) {
            $this->schemaUpdater->updateSchema($moduleSchema, $moduleMigration);
        }
    }

    /**
     * @return XML
     */
    protected function getXml()
    {
        return $this->xml;
    }
}
