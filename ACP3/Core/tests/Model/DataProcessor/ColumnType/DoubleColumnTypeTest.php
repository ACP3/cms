<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class DoubleColumnTypeTest extends AbstractColumnTypeTest
{
    protected function instantiateClassToTest()
    {
        $this->columnType = new DoubleColumnType();
    }

    public function testDoEscape()
    {
        self::assertIsFloat($this->columnType->doEscape('foo'));
        self::assertIsFloat($this->columnType->doEscape('0.00'));
        self::assertIsFloat($this->columnType->doEscape('0'));
    }
}
