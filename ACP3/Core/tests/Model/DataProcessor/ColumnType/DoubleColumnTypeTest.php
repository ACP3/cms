<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class DoubleColumnTypeTest extends AbstractColumnTypeTestCase
{
    protected function instantiateClassToTest(): void
    {
        $this->columnType = new DoubleColumnType();
    }

    public function testDoEscape(): void
    {
        self::assertIsFloat($this->columnType->doEscape('foo'));
        self::assertIsFloat($this->columnType->doEscape('0.00'));
        self::assertIsFloat($this->columnType->doEscape('0'));
    }
}
