<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

class IntegerColumnRendererTest extends AbstractColumnRendererTestCase
{
    protected function setup(): void
    {
        $this->columnRenderer = new IntegerColumnRenderer();

        parent::setUp();
    }

    public function testValidField(): void
    {
        $this->columnData = [...$this->columnData, ...[
            'fields' => ['text'],
        ]];
        $this->dbData = [
            'text' => '1',
        ];

        $expected = '<td>1</td>';
        $this->compareResults($expected);
    }

    public function testValidFieldStringToIntegerConversion(): void
    {
        $this->columnData = [...$this->columnData, ...[
            'fields' => ['text'],
        ]];
        $this->dbData = [
            'text' => 'Test',
        ];

        $expected = '<td>0</td>';
        $this->compareResults($expected);
    }
}
