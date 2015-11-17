<?php

class Nl2pColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $stringFormatter;

    protected function setUp()
    {
        $this->stringFormatter = new \ACP3\Core\Helpers\StringFormatter();

        $this->columnRenderer = new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\Nl2pColumnRenderer($this->stringFormatter);

        parent::setUp();
    }

    public function testValidField()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text']
        ]);
        $this->dbData = [
            'text' => 'Lorem Ipsum'
        ];

        $expected = '<td><p>Lorem Ipsum</p></td>';
        $this->compareResults($expected, $this->columnData, $this->dbData);
    }

    public function testValidFieldWithMultipleLines()
    {
        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text']
        ]);
        $this->dbData = [
            'text' => "Lorem Ipsum\n\nDolor"
        ];

        $expected = "<td><p>Lorem Ipsum</p>\n<p>Dolor</p></td>";
        $this->compareResults($expected, $this->columnData, $this->dbData);
    }
}