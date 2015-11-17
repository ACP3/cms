<?php

class ReplaceValueColumnRendererTest extends AbstractColumnRendererTest
{
    protected function setUp()
    {
        $this->columnRenderer = new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer();

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
        $this->compareResults($expected, $this->columnData, $this->dbData);
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
        $this->compareResults($expected, $this->columnData, $this->dbData);
    }

}