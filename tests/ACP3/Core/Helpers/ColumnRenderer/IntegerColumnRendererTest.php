<?php

class IntegerColumnRendererTest extends AbstractColumnRendererTest
{
    protected function setUp()
    {
        $this->columnRenderer = new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer();
    }

    public function testValidField()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'fields' => ['text']
        ]);
        $data = [
            'text' => '1'
        ];

        $expected = '<td>1</td>';
        $this->compareResults($expected, $column, $data);
    }

    public function testValidFieldStringToIntegerConversion()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'fields' => ['text']
        ]);
        $data = [
            'text' => 'Test'
        ];

        $expected = '<td>0</td>';
        $this->compareResults($expected, $column, $data);
    }
}