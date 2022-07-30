<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

abstract class AbstractColumnRendererTest extends \PHPUnit\Framework\TestCase
{
    protected ?AbstractColumnRenderer $columnRenderer = null;
    /**
     * @var array<string, mixed>
     */
    protected array $columnData = [];
    /**
     * @var array<string, mixed>
     */
    protected array $dbData = [];

    protected string $identifier = '';

    protected string $primaryKey = '';

    protected function setup(): void
    {
        $this->columnData = $this->getColumnDefaults();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getColumnDefaults(): array
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

    public function testSingleCustomHtmlAttribute(): void
    {
        $this->columnData = [...$this->columnData, ...[
            'attribute' => [
                'data-foo' => 'bar',
            ],
        ]];

        $expected = '<td data-foo="bar"></td>';
        $this->compareResults($expected);
    }

    public function testMultipleCustomHtmlAttributes(): void
    {
        $this->columnData = [...$this->columnData, ...[
            'attribute' => [
                'data-foo' => 'bar',
                'data-lorem' => 'ipsum',
            ],
        ]];

        $expected = '<td data-foo="bar" data-lorem="ipsum"></td>';
        $this->compareResults($expected);
    }

    public function testAddStyle(): void
    {
        $this->columnData = [...$this->columnData, ...[
            'style' => 'width:50%',
        ]];

        $expected = '<td style="width:50%"></td>';
        $this->compareResults($expected);
    }

    public function testAddCssClass(): void
    {
        $this->columnData = [...$this->columnData, ...[
            'class' => 'foobar',
        ]];

        $expected = '<td class="foobar"></td>';
        $this->compareResults($expected);
    }

    public function testInvalidField(): void
    {
        $this->columnData = [...$this->columnData, ...[
            'fields' => ['test'],
        ]];
        $this->dbData = [
            'text' => 'Lorem Ipsum',
        ];

        $expected = '<td></td>';
        $this->compareResults($expected);
    }

    public function testValidField(): void
    {
        $this->columnData = [...$this->columnData, ...[
            'fields' => ['text'],
        ]];
        $this->dbData = [
            'text' => 'Lorem Ipsum',
        ];

        $expected = '<td>Lorem Ipsum</td>';
        $this->compareResults($expected);
    }

    public function testDefaultValueIfNull(): void
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

    public function testDefaultValueIfNotFound(): void
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
