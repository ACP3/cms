<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor\ColumnType;

use ACP3\Core\Model\DataProcessor\ColumnType\TextWysiwygColumnType;

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
