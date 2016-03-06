<?php
namespace ACP3\Core\Test\Helpers\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer;

class IntegerColumnRendererTest extends AbstractColumnRendererTest
{
    protected function setUp()
    {
        $this->columnRenderer = new IntegerColumnRenderer();

        parent::setUp();
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text']
        ]);
        $this->dbData = [
            'text' => '1'
        ];

        $expected = '<td>1</td>';
        $this->compareResults($expected);
    }

    public function testValidFieldStringToIntegerConversion()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text']
        ]);
        $this->dbData = [
            'text' => 'Test'
        ];

        $expected = '<td>0</td>';
        $this->compareResults($expected);
    }
}
