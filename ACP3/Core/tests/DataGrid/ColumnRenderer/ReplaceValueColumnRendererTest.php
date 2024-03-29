<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

class ReplaceValueColumnRendererTest extends AbstractColumnRendererTestCase
{
    protected function setup(): void
    {
        parent::setup();

        $this->columnRenderer = new ReplaceValueColumnRenderer();

        $this->columnData = [...$this->getColumnDefaults(), ...[
            'custom' => [
                'search' => [],
                'replace' => [],
            ],
        ]];
    }

    public function testValidField(): void
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text'],
            'custom' => [
                'search' => ['Lorem'],
                'replace' => ['Dolor'],
            ],
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum',
        ];

        $expected = '<td>Dolor Ipsum</td>';
        $this->compareResults($expected);
    }

    public function testDefaultValueIfNull(): void
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text'],
            'custom' => [
                'default_value' => 'Foo Bar',
                'search' => [],
                'replace' => [],
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
                'search' => [],
                'replace' => [],
            ],
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum',
        ];

        $expected = '<td>Foo Bar</td>';
        $this->compareResults($expected);
    }
}
