<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Migration\Exception\NoExistingModuleMigrationsException;

class MigrationServiceLocator
{
    /**
     * @var array<string, MigrationInterface[]>
     */
    private $migrations = [];

    public function addMigration(string $moduleName, MigrationInterface $migration): void
    {
        $this->migrations[$moduleName][] = $migration;
    }

    /**
     * @return MigrationInterface[]
     */
    public function getMigrationsByModuleName(string $moduleName): array
    {
        if (!\array_key_exists($moduleName, $this->migrations)) {
            throw new NoExistingModuleMigrationsException(sprintf('Could not find any migrations for module %s!', $moduleName));
        }

        return $this->migrations[$moduleName];
    }

    public function getLatestMigrationByModuleName(string $moduleName): MigrationInterface
    {
        $migrations = $this->getMigrationsByModuleName($moduleName);
        usort($migrations, static function (MigrationInterface $a, MigrationInterface $b) {
            return $a->getSchemaVersion() <=> $b->getSchemaVersion();
        });

        return end($migrations);
    }
}
