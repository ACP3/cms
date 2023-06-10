<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

use ACP3\Core\Date;

class DateTimeColumnTypeTest extends AbstractColumnTypeTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&Date
     */
    private $dateMock;

    protected function setup(): void
    {
        $this->dateMock = $this->createMock(Date::class);

        parent::setUp();
    }

    protected function instantiateClassToTest(): void
    {
        $this->columnType = new DateTimeColumnType($this->dateMock);
    }

    public function testDoEscape(): void
    {
        $this->setUpDateMockExpectations();

        $this->columnType->doEscape('');
        $this->columnType->doEscape('2016-10-20');
    }

    private function setUpDateMockExpectations(): void
    {
        $this->dateMock->expects(self::exactly(2))
            ->method('toSQL')
            ->willReturnCallback(fn (string $value) => match ([$value]) {
                [''] => '2016-10-22 13:37:00',
                ['2016-10-20'] => '2016-10-20 00:00:00',
                default => throw new \InvalidArgumentException(),
            });
    }
}
