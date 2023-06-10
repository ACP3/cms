<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor\ColumnType;

class TextWysiwygColumnTypeTest extends AbstractColumnTypeTestCase
{
    protected function instantiateClassToTest(): void
    {
        $this->columnType = new TextWysiwygColumnType();
    }

    public function testDoEscape(): void
    {
        self::assertIsString($this->columnType->doEscape('foo'));
    }
}
