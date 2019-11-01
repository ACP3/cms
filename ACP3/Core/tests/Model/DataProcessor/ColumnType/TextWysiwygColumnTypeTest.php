<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class TextWysiwygColumnTypeTest extends TextColumnTypeTest
{
    protected function instantiateClassToTest()
    {
        $this->columnType = new TextWysiwygColumnType($this->secureMock);
    }

    protected function setUpSecureMockExpectations()
    {
        $this->secureMock->expects($this->once())
            ->method('strEncode')
            ->with('foo', true)
            ->willReturn('foo');
    }
}
