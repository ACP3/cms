<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
        $this->assertInternalType('double', $this->columnType->doEscape('foo'));
        $this->assertInternalType('double', $this->columnType->doEscape('0.00'));
        $this->assertInternalType('double', $this->columnType->doEscape('0'));
    }
}
