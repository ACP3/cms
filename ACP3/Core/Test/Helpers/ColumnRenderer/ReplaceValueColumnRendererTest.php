<?php
namespace ACP3\Core\Test\Helpers\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer;

class ReplaceValueColumnRendererTest extends AbstractColumnRendererTest
{
    protected function setUp()
    {
        $this->columnRenderer = new ReplaceValueColumnRenderer();

        $this->columnData = array_merge(
            $this->getColumnDefaults(),
            [
                'custom' => [
                    'search' => [],
                    'replace' => []
                ]
            ]
        );
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text'],
            'custom' => [
                'search' => ['Lorem'],
                'replace' => ['Dolor']
            ]
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum'
        ];

        $expected = '<td>Dolor Ipsum</td>';
        $this->compareResults($expected);
    }

    public function testDefaultValueIfNull()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text'],
            'custom' => [
                'default_value' => 'Foo Bar',
                'search' => [],
                'replace' => []
            ]
        ]);
        $this->dbData = [
            'text' => null
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
                'search' => [],
                'replace' => []
            ]
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum'
        ];

        $expected = '<td>Foo Bar</td>';
        $this->compareResults($expected);
    }

}
