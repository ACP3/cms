<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor\ColumnType;

use ACP3\Core\Model\DataProcessor\ColumnType\IntegerColumnType;

class IntegerColumnTypeTest extends AbstractColumnTypeTest
{
    protected function instantiateClassToTest()
    {
        $this->columnType = new IntegerColumnType();
    }

    public function testDoEscape()
    {
        $this->assertTrue(is_int($this->columnType->doEscape('foo')));
        $this->assertTrue(is_int($this->columnType->doEscape('0.00')));
        $this->assertTrue(is_int($this->columnType->doEscape('0')));
    }
}
