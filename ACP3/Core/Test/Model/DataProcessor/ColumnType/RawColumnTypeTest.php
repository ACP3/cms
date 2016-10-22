<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor\ColumnType;


use ACP3\Core\Model\DataProcessor\ColumnType\RawColumnType;

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
        $this->assertSame(null, $this->columnType->doEscape(null));
        $this->assertSame(true, $this->columnType->doEscape(true));
        $this->assertSame(false, $this->columnType->doEscape(false));
    }
}
