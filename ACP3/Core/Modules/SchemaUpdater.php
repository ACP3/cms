<?php

namespace ACP3\Core\Modules;

use ACP3\Core;
use ACP3\Core\Modules\Installer\MigrationInterface;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\System;

/**
 * Class SchemaUpdater
 * @package ACP3\Core\Modules
 */
class SchemaUpdater extends SchemaHelper
{
    /**
     * Führt die in der Methode schemaUpdates() enthaltenen Tabellenänderungen aus
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface    $schema
     * @param \ACP3\Core\Modules\Installer\MigrationInterface $migration
     *
     * @return int
     */
    public function updateSchema(SchemaInterface $schema, MigrationInterface $migration)
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
            // Nur für den Fall der Fälle... ;)
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
     * @param string  $moduleName
     * @param int     $schemaVersion
     * @param array   $schemaUpdates
     * @param integer $installedSchemaVersion
     *
     * @return int
     */
    protected function iterateOverSchemaUpdates($moduleName, $schemaVersion, array $schemaUpdates, $installedSchemaVersion)
    {
        $result = -1;
        foreach ($schemaUpdates as $schemaUpdateVersion => $queries) {
            // Do schema updates only, if the current schema version is older then the new one
            if ($installedSchemaVersion < $schemaUpdateVersion &&
                $schemaUpdateVersion <= $schemaVersion &&
                !empty($queries)
            ) {
                $result = $this->executeSqlQueries($this->forceSqlQueriesToArray($queries), $moduleName) === true ? 1 : 0;

                if ($result !== 0) {
                    $this->updateSchemaVersion($moduleName, $schemaUpdateVersion);
                }
            }
        }
        return $result;
    }

    /**
     * Setzt die DB-Schema-Version auf die neue Versionsnummer
     *
     * @param string  $moduleName
     * @param integer $schemaVersion
     *
     * @return bool
     */
    public function updateSchemaVersion($moduleName, $schemaVersion)
    {
        return $this->systemModuleRepository->update(['version' => (int)$schemaVersion], ['name' => $moduleName]) !== false;
    }

    /**
     * @param string|array|callable $queries
     *
     * @return array
     */
    protected function forceSqlQueriesToArray($queries)
    {
        return (is_array($queries) === false) ? (array)$queries : $queries;
    }
}
