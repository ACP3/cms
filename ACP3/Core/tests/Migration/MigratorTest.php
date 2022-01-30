<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Migration\Providers\Migration1;
use ACP3\Core\Migration\Providers\Migration123;
use ACP3\Core\Migration\Providers\Migration2;
use ACP3\Core\Migration\Repository\MigrationRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MigratorTest extends TestCase
{
    /**
     * @var MockObject|MigrationServiceLocator
     */
    private $migrationServiceLocatorMock;
    /**
     * @var MockObject|MigrationRepositoryInterface
     */
    private $migrationRepositoryMock;
    /**
     * @var Migrator
     */
    private $migrator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrationServiceLocatorMock = $this->createMock(MigrationServiceLocator::class);
        $this->migrationRepositoryMock = $this->createMock(MigrationRepositoryInterface::class);
        $this->migrator = new Migrator(
            $this->migrationServiceLocatorMock,
            $this->migrationRepositoryMock
        );
    }

    public function testUpdateModulesWithUpToDateSchemaVersion(): void
    {
        $this->migrationRepositoryMock
            ->method('findAllAlreadyExecutedMigrations')
            ->willReturn([
                Migration1::class,
                Migration2::class,
            ]);
        $this->migrationServiceLocatorMock->expects(self::once())
            ->method('getMigrations')
            ->willReturn([
                Migration1::class => new Migration1(),
                Migration2::class => new Migration2(),
            ]);

        $this->migrator->updateModules();
    }

    public function testUpdateModules(): void
    {
        $migration1Mock = $this->createMock(Migration1::class);
        $migration2Mock = $this->createMock(Migration2::class);
        $this->migrationRepositoryMock
            ->method('findAllAlreadyExecutedMigrations')
            ->willReturn([
                $migration1Mock::class,
            ]);
        $this->migrationServiceLocatorMock->expects(self::once())
            ->method('getMigrations')
            ->willReturn([
                $migration1Mock::class => $migration1Mock,
                $migration2Mock::class => $migration2Mock,
            ]);

        $migration1Mock->expects(self::never())
            ->method('up');
        $migration2Mock->expects(self::once())
            ->method('up');
        $this->migrationRepositoryMock->expects(self::once())
            ->method('insert')
            ->with(['name' => $migration2Mock::class]);

        $this->migrator->updateModules();
    }

    public function testUpdateModulesWithSingleError(): void
    {
        $migration2Mock = $this->createMock(MigrationInterface::class);
        $this->migrationRepositoryMock
            ->method('findAllAlreadyExecutedMigrations')
            ->willReturn([
                Migration1::class,
            ]);
        $this->migrationServiceLocatorMock->expects(self::once())
            ->method('getMigrations')
            ->willReturn([
                $migration2Mock::class => $migration2Mock,
            ]);

        $exception = new \Exception('Something\'s wrong here!');

        $migration2Mock->expects(self::once())
            ->method('up')
            ->willThrowException($exception);
        $migration2Mock->expects(self::once())
            ->method('down');
        $this->migrationRepositoryMock->expects(self::never())
            ->method('insert');

        self::assertSame([$migration2Mock::class => [$exception]], $this->migrator->updateModules());
    }

    public function testShouldSkipAMigrationsWithAMissingDependencies(): void
    {
        $migration123Mock = $this->createPartialMock(Migration123::class, ['up', 'down']);

        $this->migrationRepositoryMock
            ->method('findAllAlreadyExecutedMigrations')
            ->willReturn([
                Migration1::class,
            ]);
        $this->migrationServiceLocatorMock->expects(self::once())
            ->method('getMigrations')
            ->willReturn([
                $migration123Mock::class => $migration123Mock,
            ]);

        $migration123Mock->expects(self::never())
            ->method('up');
        $migration123Mock->expects(self::never())
            ->method('down');
        $this->migrationRepositoryMock->expects(self::never())
            ->method('insert');

        self::assertSame([], $this->migrator->updateModules());
    }

    public function testUpdateModulesWithErrorInDowngrade(): void
    {
        $migration2Mock = $this->createMock(MigrationInterface::class);
        $this->migrationRepositoryMock
            ->method('findAllAlreadyExecutedMigrations')
            ->willReturn([
                Migration1::class,
            ]);
        $this->migrationServiceLocatorMock->expects(self::once())
            ->method('getMigrations')
            ->willReturn([
                $migration2Mock::class => $migration2Mock,
            ]);

        $exceptionUp = new \Exception('Something\'s wrong here!');
        $exceptionDown = new \Exception('Something\'s wrong here, too!');
        $migration2Mock->expects(self::once())
            ->method('up')
            ->willThrowException($exceptionUp);
        $migration2Mock->expects(self::once())
            ->method('down')
            ->willThrowException($exceptionDown);
        $this->migrationRepositoryMock->expects(self::never())
            ->method('insert');

        self::assertSame([
            $migration2Mock::class => [
                $exceptionUp,
                $exceptionDown,
            ],
        ], $this->migrator->updateModules());
    }
}
