<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Migration\Exception\NoExistingModuleMigrationsException;
use ACP3\Core\Migration\Providers\Migration1;
use ACP3\Core\Migration\Providers\Migration2;
use PHPUnit\Framework\TestCase;

class MigrationServiceLocatorTest extends TestCase
{
    /**
     * @var MigrationServiceLocator
     */
    private $serviceLocator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serviceLocator = new MigrationServiceLocator();
    }

    public function testGetLatestMigrationByModuleName(): void
    {
        $migration = new Migration2();

        $this->serviceLocator->addMigration('foo', new Migration1());
        $this->serviceLocator->addMigration('foo', $migration);

        self::assertSame($migration, $this->serviceLocator->getLatestMigrationByModuleName('foo'));
    }

    public function testGetMigrationsByModuleName(): void
    {
        $migration1 = new Migration1();
        $migration2 = new Migration2();

        $this->serviceLocator->addMigration('foo', $migration1);
        $this->serviceLocator->addMigration('foo', $migration2);

        self::assertSame([$migration1, $migration2], $this->serviceLocator->getMigrationsByModuleName('foo'));
    }

    public function testGetMigrationsByModuleNameWithInvalidModuleName(): void
    {
        $this->expectException(NoExistingModuleMigrationsException::class);

        $this->serviceLocator->getMigrationsByModuleName('foo');
    }
}
