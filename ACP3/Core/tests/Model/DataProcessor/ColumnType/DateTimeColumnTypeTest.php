<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

use ACP3\Core\Date;

class DateTimeColumnTypeTest extends AbstractColumnTypeTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $dateMock;

    protected function setUp()
    {
        $this->dateMock = $this->createMock(Date::class);

        parent::setUp();
    }

    protected function instantiateClassToTest()
    {
        $this->columnType = new DateTimeColumnType($this->dateMock);
    }

    public function testDoEscape()
    {
        $this->setUpDateMockExpectations();

        $this->columnType->doEscape('');
        $this->columnType->doEscape('2016-10-20');
    }

    private function setUpDateMockExpectations()
    {
        $this->dateMock->expects($this->exactly(2))
            ->method('toSQL')
            ->withConsecutive([''], ['2016-10-20'])
            ->willReturnOnConsecutiveCalls('2016-10-22 13:37:00', '2016-10-20 00:00:00');
    }
}
