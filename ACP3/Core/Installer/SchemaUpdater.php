<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\Installer\Helper\SchemaHelper;
use ACP3\Modules\ACP3\System;

class SchemaUpdater extends SchemaHelper
{
    /**
     * Führt die in der Methode schemaUpdates() enthaltenen Tabellenänderungen aus
     *
     * @param \ACP3\Core\Installer\SchemaInterface $schema
     * @param \ACP3\Core\Installer\MigrationInterface $migration
     *
     * @return int
     */
    public function updateSchema(SchemaInterface $schema, MigrationInterface $migration): int
    {
        $module = $this->systemModuleRepository->getModuleSchemaVersion($schema->getModuleName());
        $installedSchemaVersion = !empty($module) ? (int)$module : 0;
        $result = -1;

        // Falls eine Methode zum Umbenennen des Moduls existiert,
        // diese mit der aktuell installierten Schemaverion aufrufen
        $moduleNames = $migration->renameModule();
        if (count($moduleNames) > 0) {
            $result = $this->iterateOverSchemaUpdates(
                $schema->getModuleName(),
                $schema->getSchemaVersion(),
                $moduleNames,
                $installedSchemaVersion
            );
        }

        $queries = $migration->schemaUpdates();
        if (is_array($queries) && count($queries) > 0) {
            ksort($queries);

            $result = $this->iterateOverSchemaUpdates(
                $schema->getModuleName(),
                $schema->getSchemaVersion(),
                $queries,
                $installedSchemaVersion
            );
        }

        return $result;
    }

    /**
     *
     * @param string $moduleName
     * @param int $schemaVersion
     * @param array $schemaUpdates
     * @param integer $installedSchemaVersion
     *
     * @return int
     */
    private function iterateOverSchemaUpdates(
        string $moduleName,
        int $schemaVersion,
        array $schemaUpdates,
        int $installedSchemaVersion
    ) {
        $result = -1;
        foreach ($schemaUpdates as $schemaUpdateVersion => $queries) {
            // Do schema updates only, if the current schema version is older then the new one
            if ($installedSchemaVersion < $schemaUpdateVersion &&
                $schemaUpdateVersion <= $schemaVersion &&
                !empty($queries)
            ) {
                $result = $this->executeSqlQueries(
                    $this->forceSqlQueriesToArray($queries),
                    $moduleName
                ) === true ? 1 : 0;

                if ($result !== 0) {
                    $this->updateSchemaVersion($moduleName, $schemaUpdateVersion);
                }
            }
        }
        return $result;
    }

    /**
     * @param string|array|callable $queries
     *
     * @return array
     */
    private function forceSqlQueriesToArray($queries): array
    {
        return (is_array($queries) === false) ? (array)$queries : $queries;
    }

    /**
     * Setzt die DB-Schema-Version auf die neue Versionsnummer
     *
     * @param string $moduleName
     * @param integer $schemaVersion
     *
     * @return bool
     */
    public function updateSchemaVersion(string $moduleName, int $schemaVersion): bool
    {
        return $this->systemModuleRepository->update(
                ['version' => $schemaVersion],
                ['name' => $moduleName]
            ) !== false;
    }
}
