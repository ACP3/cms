<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor\ColumnType;

use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;

class TextColumnTypeTest extends AbstractColumnTypeTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $secureMock;

    protected function setUp()
    {
        $this->secureMock = $this->createMock(Secure::class);

        parent::setUp();
    }

    protected function instantiateClassToTest()
    {
        $this->columnType = new TextColumnType($this->secureMock);
    }

    public function testDoEscape()
    {
        $this->setUpSecureMockExpectations();

        $this->columnType->doEscape('foo');
    }

    protected function setUpSecureMockExpectations()
    {
        $this->secureMock->expects($this->once())
            ->method('strEncode')
            ->with('foo')
            ->willReturn('foo');
    }
}
