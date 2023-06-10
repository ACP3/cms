<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class RawColumnTypeTest extends AbstractColumnTypeTestCase
{
    protected function instantiateClassToTest(): void
    {
        $this->columnType = new RawColumnType();
    }

    public function testDoEscape(): void
    {
        self::assertSame('foo', $this->columnType->doEscape('foo'));
        self::assertSame(9, $this->columnType->doEscape(9));
        self::assertSame('foo<html></html>', $this->columnType->doEscape('foo<html></html>'));
        self::assertNull($this->columnType->doEscape(null));
        self::assertTrue($this->columnType->doEscape(true));
        self::assertFalse($this->columnType->doEscape(false));
    }
}
