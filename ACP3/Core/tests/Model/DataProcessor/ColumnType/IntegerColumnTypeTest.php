<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class IntegerColumnTypeTest extends AbstractColumnTypeTest
{
    protected function instantiateClassToTest(): void
    {
        $this->columnType = new IntegerColumnType();
    }

    public function testDoEscape(): void
    {
        self::assertIsInt($this->columnType->doEscape('foo'));
        self::assertIsInt($this->columnType->doEscape('0.00'));
        self::assertIsInt($this->columnType->doEscape('0'));
    }
}
