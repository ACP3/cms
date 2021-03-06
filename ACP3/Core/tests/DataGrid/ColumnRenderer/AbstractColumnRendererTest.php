<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

abstract class AbstractColumnRendererTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \ACP3\Core\DataGrid\ColumnRenderer\AbstractColumnRenderer
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
    /**
     * @var string
     */
    protected $identifier = '';
    /**
     * @var string
     */
    protected $primaryKey = '';

    protected function setup(): void
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
            'primary' => false,
        ];
    }

    public function testSingleCustomHtmlAttribute()
    {
        $this->columnData = array_merge($this->columnData, [
            'attribute' => [
                'data-foo' => 'bar',
            ],
        ]);

        $expected = '<td data-foo="bar"></td>';
        $this->compareResults($expected);
    }

    public function testMultipleCustomHtmlAttributes()
    {
        $this->columnData = array_merge($this->columnData, [
            'attribute' => [
                'data-foo' => 'bar',
                'data-lorem' => 'ipsum',
            ],
        ]);

        $expected = '<td data-foo="bar" data-lorem="ipsum"></td>';
        $this->compareResults($expected);
    }

    public function testAddStyle()
    {
        $this->columnData = array_merge($this->columnData, [
            'style' => 'width:50%',
        ]);

        $expected = '<td style="width:50%"></td>';
        $this->compareResults($expected);
    }

    public function testAddCssClass()
    {
        $this->columnData = array_merge($this->columnData, [
            'class' => 'foobar',
        ]);

        $expected = '<td class="foobar"></td>';
        $this->compareResults($expected);
    }

    public function testInvalidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['test'],
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum',
        ];

        $expected = '<td></td>';
        $this->compareResults($expected);
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text'],
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum',
        ];

        $expected = '<td>Lorem Ipsum</td>';
        $this->compareResults($expected);
    }

    public function testDefaultValueIfNull()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text'],
            'custom' => [
                'default_value' => 'Foo Bar',
            ],
        ]);
        $this->dbData = [
            'text' => null,
        ];

        $expected = '<td>Foo Bar</td>';
        $this->compareResults($expected);
    }

    public function testDefaultValueIfNotFound()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['test'],
            'custom' => [
                'default_value' => 'Foo Bar',
            ],
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum',
        ];

        $expected = '<td>Foo Bar</td>';
        $this->compareResults($expected);
    }

    protected function compareResults(string $expected): void
    {
        $actual = $this->columnRenderer
            ->setIdentifier($this->identifier)
            ->setPrimaryKey($this->primaryKey)
            ->fetchDataAndRenderColumn($this->columnData, $this->dbData);

        self::assertEquals($expected, $actual);
    }
}
