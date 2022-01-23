<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class BooleanColumnTypeTest extends AbstractColumnTypeTest
{
    protected function instantiateClassToTest(): void
    {
        $this->columnType = new BooleanColumnType();
    }

    public function testDoEscape(): void
    {
        self::assertIsInt($this->columnType->doEscape('foo'));
        self::assertIsInt($this->columnType->doEscape(0));
        self::assertIsInt($this->columnType->doEscape('0'));
        self::assertIsInt($this->columnType->doEscape(null));
    }
}
