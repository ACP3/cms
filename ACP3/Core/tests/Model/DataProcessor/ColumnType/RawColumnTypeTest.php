<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class RawColumnTypeTest extends AbstractColumnTypeTest
{
    protected function instantiateClassToTest()
    {
        $this->columnType = new RawColumnType();
    }

    public function testDoEscape()
    {
        $this->assertSame('foo', $this->columnType->doEscape('foo'));
        $this->assertSame(9, $this->columnType->doEscape(9));
        $this->assertSame('foo<html></html>', $this->columnType->doEscape('foo<html></html>'));
        $this->assertNull($this->columnType->doEscape(null));
        $this->assertTrue($this->columnType->doEscape(true));
        $this->assertFalse($this->columnType->doEscape(false));
    }
}
