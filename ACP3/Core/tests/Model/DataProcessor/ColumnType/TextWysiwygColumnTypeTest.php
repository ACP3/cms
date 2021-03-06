<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class TextWysiwygColumnTypeTest extends AbstractColumnTypeTest
{
    protected function instantiateClassToTest()
    {
        $this->columnType = new TextWysiwygColumnType();
    }

    public function testDoEscape()
    {
        self::assertIsString($this->columnType->doEscape('foo'));
    }
}
