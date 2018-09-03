<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor\ColumnType;

use ACP3\Core\Model\DataProcessor\ColumnType\BooleanColumnType;

class BooleanColumnTypeTest extends AbstractColumnTypeTest
{
    protected function instantiateClassToTest()
    {
        $this->columnType = new BooleanColumnType();
    }

    public function testDoEscape()
    {
        $this->assertInternalType('bool', $this->columnType->doEscape('foo'));
        $this->assertInternalType('bool', $this->columnType->doEscape(0));
        $this->assertInternalType('bool', $this->columnType->doEscape('0'));
        $this->assertInternalType('bool', $this->columnType->doEscape(null));
    }
}
