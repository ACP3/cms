<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Modules\Installer\MigrationInterface;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Core\Repository\ModuleAwareRepositoryInterface;

class SchemaUpdater
{
    /**
     * @var \ACP3\Core\Modules\SchemaHelper
     */
    private $schemaHelper;
    /**
     * @var \ACP3\Core\Repository\ModuleAwareRepositoryInterface
     */
    private $moduleAwareRepository;

    public function __construct(SchemaHelper $schemaHelper, ModuleAwareRepositoryInterface $moduleAwareRepository)
    {
        $this->schemaHelper = $schemaHelper;
        $this->moduleAwareRepository = $moduleAwareRepository;
    }

    /**
     * Execute the schema updates which can be found within the methods `renameModule` and `schemaUpdates`.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     * @throws \ACP3\Core\Modules\Exception\ModuleMigrationException
     */
    public function updateSchema(SchemaInterface $schema, MigrationInterface $migration): void
    {
        $installedSchemaVersion = $this->moduleAwareRepository->getModuleSchemaVersion($schema->getModuleName());
        $normalizedSchemaMigrations = $this->mergeSchemaMigrations($migration->renameModule(), $migration->schemaUpdates());

        $this->iterateOverSchemaUpdates(
            $schema->getModuleName(),
            $normalizedSchemaMigrations,
            $installedSchemaVersion
        );
    }

    /**
     * @param Array<int, string|callable|string[]|callable[]> $moduleRenameMigrations
     * @param Array<int, string|callable|string[]|callable[]> $schemaMigrations
     *
     * @return Array<int, string[]|callable[]>
     */
    private function mergeSchemaMigrations(array $moduleRenameMigrations, array $schemaMigrations): array
    {
        $normalizedModuleRenameMigrations = $this->prepareMigrationsForRecursiveMerge($moduleRenameMigrations);
        $normalizedSchemaMigrations = $this->prepareMigrationsForRecursiveMerge($schemaMigrations);

        $mergedMigrations = array_merge_recursive($normalizedModuleRenameMigrations, $normalizedSchemaMigrations);

        return $this->normalizeMigrationsAfterRecursiveMerge($mergedMigrations);
    }

    /**
     * @param Array<int, string[]|callable[]> $schemaUpdates
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     * @throws \ACP3\Core\Modules\Exception\ModuleMigrationException
     */
    private function iterateOverSchemaUpdates(
        string $moduleName,
        array $schemaUpdates,
        int $installedSchemaVersion
    ): void {
        ksort($schemaUpdates);

        $latestSchemaVersion = array_key_last($schemaUpdates);

        foreach ($schemaUpdates as $newSchemaVersion => $queries) {
            // Do schema updates only, if the current schema version is older then the new one
            if ($installedSchemaVersion < $newSchemaVersion
                && $newSchemaVersion <= $latestSchemaVersion
                && !empty($queries)
            ) {
                $this->schemaHelper->executeSqlQueries($queries, $moduleName);
                $this->updateSchemaVersion($moduleName, $newSchemaVersion);
            }
        }
    }

    /**
     * Setzt die DB-Schema-Version auf die neue Versionsnummer.
     */
    private function updateSchemaVersion(string $moduleName, int $schemaVersion): bool
    {
        return $this->moduleAwareRepository->update(['version' => $schemaVersion], ['name' => $moduleName]) !== false;
    }

    /**
     * @param array<int, string|callable|array<string|callable>> $migrations
     *
     * @return array<int, array<string|callable>>
     */
    private function prepareMigrationsForRecursiveMerge(array $migrations): array
    {
        $migrationsNew = [];
        foreach ($migrations as $schemaVersion => $queries) {
            // We need to change the index to be non-numeric,
            // as array_merge/array_merge_recursive doesn't preserve numeric key
            $migrationsNew['index_' . $schemaVersion] = \is_array($queries) ? $queries : [$queries];
        }

        /* @phpstan-ignore-next-line */
        return $migrationsNew;
    }

    /**
     * @param Array<string, string[]|callable[]> $migrations
     *
     * @return Array<int, string[]|callable[]>
     */
    private function normalizeMigrationsAfterRecursiveMerge(array $migrations): array
    {
        $migrationsNormalized = [];

        foreach ($migrations as $schemaVersion => $queries) {
            if (!\is_array($queries) || strpos($schemaVersion, 'index_') !== 0) {
                throw new \InvalidArgumentException('Please call method "prepareMigrationsForRecursiveMerge" before calling ' . __METHOD__ . '!');
            }

            $migrationsNormalized[(int) substr($schemaVersion, 6)] = $queries;
        }

        return $migrationsNormalized;
    }
}
