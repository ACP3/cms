<?php
namespace ACP3\Core\Test\Helpers\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\MassActionColumnRenderer;

class MassActionColumnRendererTest extends AbstractColumnRendererTest
{
    protected function setUp()
    {
        $this->columnRenderer = new MassActionColumnRenderer();

        parent::setUp();
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'custom' => [
                'can_delete' => true
            ],
        ]);
        $this->dbData = [
            'id' => 1234
        ];

        $this->primaryKey = 'id';

        $expected = '<td><input type="checkbox" name="entries[]" value="1234"></td>';
        $this->compareResults($expected);
    }


}
