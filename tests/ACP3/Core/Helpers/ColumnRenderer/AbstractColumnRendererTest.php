<?php

abstract class AbstractColumnRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer
     */
    protected $columnRenderer;

    protected function setUp()
    {

    }

    /**
     * @return array
     */
    protected function getColumnDefaults()
    {
        return [
            'label' => '',
            'type' => '',
            'fields' => [],
            'class' => '',
            'style' => '',
            'sortable' => true,
            'default_sort' => false,
            'default_sort_direction' => 'asc',
            'custom' => [],
            'attribute' => [],
            'primary' => false
        ];
    }

    public function testCustomHtmlAttribute()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'attribute' => [
                'data-foo' => 'bar'
            ]
        ]);

        $expected = '<td data-foo="bar"></td>';
        $actual = $this->columnRenderer->fetchDataAndRenderColumn($column, [], '', '');

        $this->assertEquals($expected, $actual);
    }
}