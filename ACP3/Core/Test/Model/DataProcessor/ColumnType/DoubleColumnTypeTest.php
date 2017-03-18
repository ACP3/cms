<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor\ColumnType;

use ACP3\Core\Model\DataProcessor\ColumnType\DoubleColumnType;

class DoubleColumnTypeTest extends AbstractColumnTypeTest
{
    protected function instantiateClassToTest()
    {
        $this->columnType = new DoubleColumnType();
    }

    public function testDoEscape()
    {
        $this->assertTrue(is_double($this->columnType->doEscape('foo')));
        $this->assertTrue(is_double($this->columnType->doEscape('0.00')));
        $this->assertTrue(is_double($this->columnType->doEscape('0')));
    }
}
