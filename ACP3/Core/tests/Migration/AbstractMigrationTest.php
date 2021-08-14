<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Migration\Exception\WrongMigrationNameException;
use PHPUnit\Framework\TestCase;

class AbstractMigrationTest extends TestCase
{
    public function testGetSchemaVersion(): void
    {
        self::assertSame(1, (new Migration1())->getSchemaVersion());
        self::assertSame(123, (new Migration123())->getSchemaVersion());
    }

    public function testGetSchemaVersionWithInvalidMigrationName(): void
    {
        $this->expectException(WrongMigrationNameException::class);

        (new Migration1Invalid())->getSchemaVersion();
        (new InvalidMigration1())->getSchemaVersion();
    }
}

class Migration1 extends AbstractMigration
{
    public function up(): void
    {
    }

    public function down(): void
    {
    }
}

class Migration123 extends AbstractMigration
{
    public function up(): void
    {
    }

    public function down(): void
    {
    }
}

class Migration1Invalid extends AbstractMigration
{
    public function up(): void
    {
    }

    public function down(): void
    {
    }
}

class InvalidMigration1 extends AbstractMigration
{
    public function up(): void
    {
    }

    public function down(): void
    {
    }
}
