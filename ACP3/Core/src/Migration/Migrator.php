<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\Repository\MigrationRepositoryInterface;

class Migrator
{
    /**
     * @var Connection
     */
    private $db;
    /**
     * @var MigrationServiceLocator
     */
    private $migrationServiceLocator;
    /**
     * @var MigrationRepositoryInterface
     */
    private $migrationRepository;

    public function __construct(
        Connection $db,
        MigrationServiceLocator $migrationServiceLocator,
        MigrationRepositoryInterface $migrationRepository
    ) {
        $this->db = $db;
        $this->migrationServiceLocator = $migrationServiceLocator;
        $this->migrationRepository = $migrationRepository;
    }

    /**
     * @return array<string, \Throwable[]|null>
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function updateModules(): array
    {
        $migrations = $this->migrationServiceLocator->getMigrations();

        $result = [];
        foreach ($migrations as $fqcn => $migration) {
            // We need to update the already executed migrations as migration are (theoretically)
            // allowed to update/modify the data within the "migration" table, too.
            if (\in_array($fqcn, $this->migrationRepository->findAllAlreadyExecutedMigrations(), true)) {
                continue;
            }

            $result[$fqcn] = $this->migrate($migration);
        }

        return $result;
    }

    /**
     * @return \Throwable[]|null
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function migrate(MigrationInterface $migration): ?array
    {
        $this->db->beginTransaction();

        try {
            $migration->up();

            $this->markMigrationAsExecuted($migration);

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

            return $collectedErrors;
        }

        return null;
    }

    private function markMigrationAsExecuted(MigrationInterface $migration): void
    {
        $this->migrationRepository->insert(['name' => \get_class($migration)]);
    }
}
