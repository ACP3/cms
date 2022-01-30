<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Migration\Repository\MigrationRepositoryInterface;

class Migrator
{
    public function __construct(private MigrationServiceLocator $migrationServiceLocator, private MigrationRepositoryInterface $migrationRepository)
    {
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
            // We need to update the already executed migrations as migrations are (theoretically)
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
        try {
            $migration->up();

            $this->markMigrationAsExecuted($migration);
        } catch (\Throwable $e) {
            $collectedErrors = [$e];

            // Attempt to rollback the faulty migration
            try {
                $migration->down();
            } catch (\Throwable $rollbackException) {
                $collectedErrors[] = $rollbackException;
            }

            return $collectedErrors;
        }

        return null;
    }

    private function markMigrationAsExecuted(MigrationInterface $migration): void
    {
        $this->migrationRepository->insert(['name' => $migration::class]);
    }
}
