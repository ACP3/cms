<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use MJS\TopSort\Implementations\StringSort;

class MigrationServiceLocator
{
    /**
     * @var array<string, MigrationInterface>
     */
    private $migrations = [];

    public function addMigration(MigrationInterface $migration): void
    {
        $this->migrations[\get_class($migration)] = $migration;
    }

    /**
     * @return array<string, MigrationInterface>
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function getMigrations(): array
    {
        $topSort = new StringSort();

        foreach ($this->migrations as $fqcn => $migration) {
            $topSort->add($fqcn, $migration->dependencies());
        }

        $migrationsTopSorted = [];
        foreach ($topSort->sort() as $fqcn) {
            $migrationsTopSorted[$fqcn] = $this->migrations[$fqcn];
        }

        return $migrationsTopSorted;
    }
}
