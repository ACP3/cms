<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\Exception\ModuleMigrationException;
use ACP3\Core\Migration\Providers\Migration1;
use ACP3\Core\Migration\Providers\Migration2;
use ACP3\Core\Repository\ModuleAwareRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MigratorTest extends TestCase
{
    /**
     * @var MockObject & Connection
     */
    private $dbMock;
    /**
     * @var MockObject & LoggerInterface
     */
    private $loggerMock;
    /**
     * @var MockObject & MigrationServiceLocator
     */
    private $migrationServiceLocatorMock;
    /**
     * @var MockObject & ModuleAwareRepositoryInterface
     */
    private $moduleAwareRepositoryMock;
    /**
     * @var Migrator
     */
    private $migrator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbMock = $this->createMock(Connection::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->migrationServiceLocatorMock = $this->createMock(MigrationServiceLocator::class);
        $this->moduleAwareRepositoryMock = $this->createMock(ModuleAwareRepositoryInterface::class);
        $this->migrator = new Migrator(
            $this->dbMock,
            $this->loggerMock,
            $this->migrationServiceLocatorMock,
            $this->moduleAwareRepositoryMock
        );
    }

    public function testUpdateModuleWithUpToDateSchemaVersion(): void
    {
        $this->moduleAwareRepositoryMock->expects(self::once())
            ->method('getModuleSchemaVersion')
            ->with('foo')
            ->willReturn(2);
        $this->migrationServiceLocatorMock->expects(self::once())
            ->method('getMigrationsByModuleName')
            ->with('foo')
            ->willReturn([
                new Migration1(),
                new Migration2(),
            ]);

        $this->dbMock->expects(self::never())
            ->method('beginTransaction');

        $this->migrator->updateModule('foo');
    }

    public function testUpdateModule(): void
    {
        $migration1Mock = $this->createMock(MigrationInterface::class);
        $migration2Mock = $this->createMock(MigrationInterface::class);
        $this->moduleAwareRepositoryMock->expects(self::once())
            ->method('getModuleSchemaVersion')
            ->with('foo')
            ->willReturn(1);
        $this->migrationServiceLocatorMock->expects(self::once())
            ->method('getMigrationsByModuleName')
            ->with('foo')
            ->willReturn([
                $migration1Mock,
                $migration2Mock,
            ]);

        $this->dbMock->expects(self::once())
            ->method('beginTransaction');
        $this->dbMock->expects(self::once())
            ->method('commit');

        $migration1Mock
            ->method('getSchemaVersion')
            ->willReturn(1);
        $migration1Mock->expects(self::never())
            ->method('up');
        $migration2Mock
            ->method('getSchemaVersion')
            ->willReturn(2);
        $migration2Mock->expects(self::once())
            ->method('up');
        $this->moduleAwareRepositoryMock->expects(self::once())
            ->method('update')
            ->with(['version' => 2], ['name' => 'foo']);

        $this->migrator->updateModule('foo');
    }

    public function testUpdateModuleWithSingleError(): void
    {
        $this->expectException(ModuleMigrationException::class);

        $migration2Mock = $this->createMock(MigrationInterface::class);
        $this->moduleAwareRepositoryMock->expects(self::once())
            ->method('getModuleSchemaVersion')
            ->with('foo')
            ->willReturn(1);
        $this->migrationServiceLocatorMock->expects(self::once())
            ->method('getMigrationsByModuleName')
            ->with('foo')
            ->willReturn([
                $migration2Mock,
            ]);

        $this->dbMock->expects(self::once())
            ->method('beginTransaction');
        $this->dbMock->expects(self::once())
            ->method('commit');

        $migration2Mock
            ->method('getSchemaVersion')
            ->willReturn(2);
        $migration2Mock->expects(self::once())
            ->method('up')
            ->willThrowException(new \Exception('Something\'s wrong here!'));
        $migration2Mock->expects(self::once())
            ->method('down');
        $this->moduleAwareRepositoryMock->expects(self::never())
            ->method('update');

        $this->migrator->updateModule('foo');
    }

    public function testUpdateModuleWithErrorInDowngrade(): void
    {
        $this->expectException(ModuleMigrationException::class);

        $migration2Mock = $this->createMock(MigrationInterface::class);
        $this->moduleAwareRepositoryMock->expects(self::once())
            ->method('getModuleSchemaVersion')
            ->with('foo')
            ->willReturn(1);
        $this->migrationServiceLocatorMock->expects(self::once())
            ->method('getMigrationsByModuleName')
            ->with('foo')
            ->willReturn([
                $migration2Mock,
            ]);

        $this->dbMock->expects(self::once())
            ->method('beginTransaction');
        $this->dbMock->expects(self::never())
            ->method('commit');
        $this->dbMock->expects(self::once())
            ->method('rollback');

        $migration2Mock
            ->method('getSchemaVersion')
            ->willReturn(2);
        $migration2Mock->expects(self::once())
            ->method('up')
            ->willThrowException(new \Exception('Something\'s wrong here!'));
        $migration2Mock->expects(self::once())
            ->method('down')
            ->willThrowException(new \Exception('Something\'s wrong here, too!'));
        $this->moduleAwareRepositoryMock->expects(self::never())
            ->method('update');

        $this->migrator->updateModule('foo');
    }
}
