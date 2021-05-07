<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

class MassActionColumnRendererTest extends AbstractColumnRendererTest
{
    protected function setup(): void
    {
        $this->columnRenderer = new MassActionColumnRenderer();

        parent::setUp();
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'custom' => [
                'can_delete' => true,
            ],
        ]);
        $this->dbData = [
            'id' => 1234,
        ];

        $this->primaryKey = 'id';

        $expected = '<td><input type="checkbox" name="entries[]" value="1234"></td>';
        $this->compareResults($expected);
    }
}
