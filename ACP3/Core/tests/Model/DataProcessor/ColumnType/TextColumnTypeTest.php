<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

use ACP3\Core\Helpers\Secure;

class TextColumnTypeTest extends AbstractColumnTypeTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Secure
     */
    protected $secureMock;

    protected function setup(): void
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
        $this->secureMock->expects(self::once())
            ->method('strEncode')
            ->with('foo')
            ->willReturn('foo');
    }
}
