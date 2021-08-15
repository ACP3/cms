<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\Exception\ModuleMigrationException;
use ACP3\Core\Repository\ModuleAwareRepositoryInterface;
use Psr\Log\LoggerInterface;

class Migrator
{
    /**
     * @var Connection
     */
    private $db;
    /**
     * @var LoggerInterface
     */
    private $migrationLogger;
    /**
     * @var MigrationServiceLocator
     */
    private $serviceLocator;
    /**
     * @var ModuleAwareRepositoryInterface
     */
    private $moduleRepository;

    public function __construct(
        Connection $db,
        LoggerInterface $migrationLogger,
        MigrationServiceLocator $serviceLocator,
        ModuleAwareRepositoryInterface $moduleRepository
    ) {
        $this->db = $db;
        $this->migrationLogger = $migrationLogger;
        $this->serviceLocator = $serviceLocator;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateModule(string $moduleName): void
    {
        foreach ($this->findNecessaryMigrations($moduleName) as $migration) {
            $this->db->beginTransaction();

            try {
                $migration->up();

                $this->updateSchemaVersion($moduleName, $migration->getSchemaVersion());

                $this->db->commit();
            } catch (\Throwable $e) {
                $collectedErrors = [$e];

                // Attempt to rollback the faulty migration
                try {
                    $migration->down();

                    $this->db->commit();
                } catch (\Throwable $rollbackException) {
                    $collectedErrors[] = $rollbackException;

                    $this->db->rollBack();
                }

                foreach ($collectedErrors as $exception) {
                    $this->migrationLogger->error($exception->getMessage());
                }

                throw new ModuleMigrationException(sprintf('An error occurred while running the migration "%d" for module "%s": %s', $migration->getSchemaVersion(), $moduleName, implode(', ', $collectedErrors)));
            }
        }
    }

    private function updateSchemaVersion(string $moduleName, int $schemaVersion): void
    {
        $this->moduleRepository->update(['version' => $schemaVersion], ['name' => $moduleName]);
    }

    /**
     * @return MigrationInterface[]
     */
    private function findNecessaryMigrations(string $moduleName): array
    {
        $installedSchemaVersion = $this->moduleRepository->getModuleSchemaVersion($moduleName);

        $migrations = $this->serviceLocator->getMigrationsByModuleName($moduleName);
        $migrations = array_filter($migrations, static function (MigrationInterface $migration) use ($installedSchemaVersion) {
            return $migration->getSchemaVersion() > $installedSchemaVersion;
        });
        usort($migrations, static function (MigrationInterface $a, MigrationInterface $b) {
            return $a->getSchemaVersion() <=> $b->getSchemaVersion();
        });

        return $migrations;
    }
}
