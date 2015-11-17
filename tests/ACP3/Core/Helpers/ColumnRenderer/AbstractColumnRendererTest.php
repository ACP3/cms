<?php

abstract class AbstractColumnRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer
     */
    protected $columnRenderer;
    /**
     * @var array
     */
    protected $columnData = [];
    /**
     * @var array
     */
    protected $dbData = [];

    protected function setUp()
    {
        $this->columnData = $this->getColumnDefaults();
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
        $this->columnData = array_merge($this->columnData, [
            'attribute' => [
                'data-foo' => 'bar'
            ]
        ]);

        $expected = '<td data-foo="bar"></td>';
        $this->compareResults($expected, $this->columnData);
    }

    public function testMultipleCustomHtmlAttributes()
    {
        $this->columnData = array_merge($this->columnData, [
            'attribute' => [
                'data-foo' => 'bar',
                'data-lorem' => 'ipsum',
            ]
        ]);

        $expected = '<td data-foo="bar" data-lorem="ipsum"></td>';
        $this->compareResults($expected, $this->columnData);
    }

    public function testAddStyle()
    {
        $this->columnData = array_merge($this->columnData, [
            'style' => 'width:50%'
        ]);

        $expected = '<td style="width:50%"></td>';
        $this->compareResults($expected, $this->columnData);
    }

    public function testAddCssClass()
    {
        $this->columnData = array_merge($this->columnData, [
            'class' => 'foobar'
        ]);

        $expected = '<td class="foobar"></td>';
        $this->compareResults($expected, $this->columnData);
    }

    public function testInvalidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['test']
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum'
        ];

        $expected = '<td></td>';
        $this->compareResults($expected, $this->columnData, $this->dbData);
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text']
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum'
        ];

        $expected = '<td>Lorem Ipsum</td>';
        $this->compareResults($expected, $this->columnData, $this->dbData);
    }

    public function testDefaultValueIfNull()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text'],
            'custom' => [
                'default_value' => 'Foo Bar'
            ]
        ]);
        $this->dbData = [
            'text' => null
        ];

        $expected = '<td>Foo Bar</td>';
        $this->compareResults($expected, $this->columnData, $this->dbData);
    }

    public function testDefaultValueIfNotFound()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['test'],
            'custom' => [
                'default_value' => 'Foo Bar'
            ]
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum'
        ];

        $expected = '<td>Foo Bar</td>';
        $this->compareResults($expected, $this->columnData, $this->dbData);
    }

    /**
     * @param string $expected
     * @param array  $column
     * @param array  $data
     */
    protected function compareResults($expected, array $column, array $data = [])
    {
        $actual = $this->columnRenderer->fetchDataAndRenderColumn($column, $data, '', '');

        $this->assertEquals($expected, $actual);
    }
}