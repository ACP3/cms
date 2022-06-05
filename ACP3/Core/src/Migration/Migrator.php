<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Migration\Repository\MigrationRepositoryInterface;

class Migrator
{
    public function __construct(private readonly MigrationServiceLocator $migrationServiceLocator, private readonly MigrationRepositoryInterface $migrationRepository)
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
            $alreadyExecutedMigrations = $this->migrationRepository->findAllAlreadyExecutedMigrations();

            // We need to update the already executed migrations as migrations are (theoretically)
            // allowed to update/modify the data within the "migration" table, too.
            if (\in_array($fqcn, $alreadyExecutedMigrations, true)) {
                continue;
            }

            // We need to ensure, that the other migrations a certain migration requires, have already been executed.
            // Otherwise, this can result unforeseeable errors.
            if ($migration->dependencies() !== null && \count(array_intersect($migration->dependencies(), $alreadyExecutedMigrations)) !== \count($migration->dependencies())) {
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
