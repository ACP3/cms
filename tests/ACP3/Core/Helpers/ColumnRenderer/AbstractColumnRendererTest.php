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

    public function testSingleCustomHtmlAttribute()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'attribute' => [
                'data-foo' => 'bar'
            ]
        ]);

        $expected = '<td data-foo="bar"></td>';
        $this->compareResults($expected, $column);
    }

    public function testMultipleCustomHtmlAttributes()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'attribute' => [
                'data-foo' => 'bar',
                'data-lorem' => 'ipsum',
            ]
        ]);

        $expected = '<td data-foo="bar" data-lorem="ipsum"></td>';
        $this->compareResults($expected, $column);
    }

    public function testAddStyle()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'style' => 'width:50%'
        ]);

        $expected = '<td style="width:50%"></td>';
        $this->compareResults($expected, $column);
    }

    public function testAddCssClass()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'class' => 'foobar'
        ]);

        $expected = '<td class="foobar"></td>';
        $this->compareResults($expected, $column);
    }

    public function testInvalidField()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'fields' => ['test']
        ]);
        $data = [
            'text' => 'Lorem Ipsum'
        ];

        $expected = '<td></td>';
        $this->compareResults($expected, $column, $data);
    }

    public function testValidField()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'fields' => ['text']
        ]);
        $data = [
            'text' => 'Lorem Ipsum'
        ];

        $expected = '<td>Lorem Ipsum</td>';
        $this->compareResults($expected, $column, $data);
    }

    public function testDefaultValueIfNull()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'fields' => ['text'],
            'custom' => [
                'default_value' => 'Foo Bar'
            ]
        ]);
        $data = [
            'text' => null
        ];

        $expected = '<td>Foo Bar</td>';
        $this->compareResults($expected, $column, $data);
    }

    public function testDefaultValueIfNotFound()
    {
        $column = array_merge($this->getColumnDefaults(), [
            'fields' => ['test'],
            'custom' => [
                'default_value' => 'Foo Bar'
            ]
        ]);
        $data = [
            'text' => 'Lorem Ipsum'
        ];

        $expected = '<td>Foo Bar</td>';
        $this->compareResults($expected, $column, $data);
    }

    /**
     * @param       $expected
     * @param array $column
     * @param array $data
     */
    protected function compareResults($expected, array $column, array $data = [])
    {
        $actual = $this->columnRenderer->fetchDataAndRenderColumn($column, $data, '', '');

        $this->assertEquals($expected, $actual);
    }
}