<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Modules\Installer\MigrationInterface;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;
use PHPUnit\Framework\TestCase;

class SchemaUpdaterTest extends TestCase
{
    /**
     * @var \ACP3\Core\Modules\SchemaUpdater
     */
    private $schemaUpdater;
    /**
     * @var \ACP3\Core\Modules\SchemaHelper & \PHPUnit\Framework\MockObject\MockObject
     */
    private $schemaHelper;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository &\PHPUnit\Framework\MockObject\MockObject
     */
    private $moduleRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schemaHelper = $this->createMock(SchemaHelper::class);
        $this->moduleRepositoryMock = $this->createMock(ModulesRepository::class);

        $this->schemaUpdater = new SchemaUpdater(
            $this->schemaHelper,
            $this->moduleRepositoryMock
        );
    }

    public function testUpdateSchema(): void
    {
        $closure = static function () {
            return true;
        };

        $schemaMock = $this->createMock(SchemaInterface::class);
        $migrationMock = $this->createMock(MigrationInterface::class);

        $schemaMock->expects(self::atLeastOnce())
            ->method('getModuleName')
            ->willReturn('foo');

        $migrationMock->expects(self::once())
            ->method('renameModule')
            ->willReturn([
                3 => 'UPDATE foo SET bar = \'bar\';',
            ]);
        $migrationMock->expects(self::once())
            ->method('schemaUpdates')
            ->willReturn([
                2 => 'SELECT fake FROM news;',
                3 => [
                    'ALTER TABLE foo;',
                    $closure,
                ],
            ]);

        $this->moduleRepositoryMock->expects(self::once())
            ->method('getModuleSchemaVersion')
            ->with('foo')
            ->willReturn(1);

        $this->schemaHelper->expects(self::atLeastOnce())
            ->method('executeSqlQueries')
            ->withConsecutive(
                [
                    ['SELECT fake FROM news;'],
                    'foo',
                ],
                [
                    [
                        'UPDATE foo SET bar = \'bar\';',
                        'ALTER TABLE foo;',
                        $closure,
                    ],
                    'foo',
                ]
            );
        $this->moduleRepositoryMock->expects(self::atLeastOnce())
            ->method('update');

        $this->schemaUpdater->updateSchema($schemaMock, $migrationMock);
    }
}
