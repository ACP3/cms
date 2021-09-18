<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Migration\Providers\Migration1;
use ACP3\Core\Migration\Providers\Migration123;
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

    public function testGetMigrationsTopSorted(): void
    {
        $migration1 = new Migration1();
        $migration2 = new Migration2();
        $migration123 = new Migration123();
        $this->serviceLocator->addMigration($migration1);
        $this->serviceLocator->addMigration($migration2);
        $this->serviceLocator->addMigration($migration123);

        self::assertSame(
            [Migration1::class => $migration1, Migration2::class => $migration2, Migration123::class => $migration123],
            $this->serviceLocator->getMigrations()
        );
    }
}
