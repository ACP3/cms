<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Modules\Installer\MigrationInterface;
use ACP3\Core\Modules\Installer\SchemaInterface;

class SchemaUpdater extends SchemaHelper
{
    /**
     * Führt die in der Methode schemaUpdates() enthaltenen Tabellenänderungen aus.
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface    $schema
     * @param \ACP3\Core\Modules\Installer\MigrationInterface $migration
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Modules\Exception\ModuleMigrationException
     */
    public function updateSchema(SchemaInterface $schema, MigrationInterface $migration): void
    {
        $module = $this->systemModuleRepository->getModuleSchemaVersion($schema->getModuleName());
        $installedSchemaVersion = !empty($module) ? $module : 0;

        // Falls eine Methode zum Umbenennen des Moduls existiert,
        // diese mit der aktuell installierten Schemaverion aufrufen
        $moduleNames = $migration->renameModule();
        if (\count($moduleNames) > 0) {
            $this->iterateOverSchemaUpdates(
                $schema->getModuleName(),
                $schema->getSchemaVersion(),
                $moduleNames,
                $installedSchemaVersion
            );
        }

        $queries = $migration->schemaUpdates();
        if (\is_array($queries) && \count($queries) > 0) {
            // Nur für den Fall der Fälle... ;)
            \ksort($queries);

            $this->iterateOverSchemaUpdates(
                $schema->getModuleName(),
                $schema->getSchemaVersion(),
                $queries,
                $installedSchemaVersion
            );
        }
    }

    /**
     * @param string $moduleName
     * @param int    $schemaVersion
     * @param array  $schemaUpdates
     * @param int    $installedSchemaVersion
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Modules\Exception\ModuleMigrationException
     */
    protected function iterateOverSchemaUpdates(
        $moduleName,
        $schemaVersion,
        array $schemaUpdates,
        $installedSchemaVersion
    ): void {
        foreach ($schemaUpdates as $schemaUpdateVersion => $queries) {
            // Do schema updates only, if the current schema version is older then the new one
            if ($installedSchemaVersion < $schemaUpdateVersion
                && $schemaUpdateVersion <= $schemaVersion
                && !empty($queries)
            ) {
                $this->executeSqlQueries($this->forceSqlQueriesToArray($queries), $moduleName);
                $this->updateSchemaVersion($moduleName, $schemaUpdateVersion);
            }
        }
    }

    /**
     * Setzt die DB-Schema-Version auf die neue Versionsnummer.
     *
     * @param string $moduleName
     * @param int    $schemaVersion
     *
     * @return bool
     */
    public function updateSchemaVersion(string $moduleName, int $schemaVersion): bool
    {
        return $this->systemModuleRepository->update(['version' => $schemaVersion], ['name' => $moduleName]) !== false;
    }

    /**
     * @param string|array|callable $queries
     *
     * @return array
     */
    protected function forceSqlQueriesToArray($queries): array
    {
        return (\is_array($queries) === false) ? (array) $queries : $queries;
    }
}
