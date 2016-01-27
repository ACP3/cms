<?php

class DataGridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Helpers\DataGrid|PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataGrid;
    /**
     * @var \ACP3\Core\ACL|PHPUnit_Framework_MockObject_MockObject
     */
    protected $aclMock;
    /**
     * @var \ACP3\Core\I18n\Translator|PHPUnit_Framework_MockObject_MockObject
     */
    protected $langMock;

    protected function setUp()
    {
        $this->aclMock = $this->getMockBuilder(\ACP3\Core\ACL::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasPermission'])
            ->getMock();

        $this->langMock = $this->getMockBuilder(\ACP3\Core\I18n\Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();

        $this->dataGrid = new \ACP3\Core\Helpers\DataGrid(
            $this->aclMock,
            $this->langMock
        );
        $this->dataGrid->setIdentifier('#data-grid');

        parent::setUp();
    }

    /**
     * @return array
     */
    protected function getDefaultExpected()
    {
        return [
            'can_edit' => false,
            'can_delete' => false,
            'identifier' => 'data-grid',
            'header' => '',
            'config' => [
                'element' => '#data-grid',
                'records_per_page' => 10,
                'hide_col_sort' => "0",
                'sort_col' => null,
                'sort_dir' => null
            ],
            'results' => ''
        ];
    }

    public function testRenderWithDefaults()
    {
        $this->aclMock
            ->expects($this->exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        $expected = $this->getDefaultExpected();

        $this->assertEquals($expected, $this->dataGrid->render());
    }

    public function testRenderWithOneTextColumn()
    {
        $this->aclMock
            ->expects($this->exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        /** @var \ACP3\Core\Helpers\Formatter\MarkEntries|PHPUnit_Framework_MockObject_MockObject $markEntriesMock */
        $markEntriesMock = $this->getMockBuilder(\ACP3\Core\Helpers\Formatter\MarkEntries::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataGrid->registerColumnRenderer(new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\HeaderColumnRenderer($markEntriesMock));
        $this->dataGrid->registerColumnRenderer(new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer());

        $this->dataGrid->addColumn([
            'label' => 'Foo',
            'fields' => ['title'],
            'type' => \ACP3\Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class
        ], 10);

        $expected = array_merge(
            $this->getDefaultExpected(),
            [
                'header' => '<th>Foo</th>',
                'config' => [
                    'element' => '#data-grid',
                    'records_per_page' => 10,
                    'hide_col_sort' => "1",
                    'sort_col' => null,
                    'sort_dir' => null
                ],
            ]
        );

        $this->assertEquals($expected, $this->dataGrid->render());
    }

    public function testRenderWithOneTextColumnAndData()
    {
        $data = [
            [
                'title' => 'Lorem Ipsum'
            ],
            [
                'title' => 'Lorem Ipsum Dolor'
            ]
        ];
        $this->aclMock
            ->expects($this->exactly(2))
            ->method('hasPermission')
            ->willReturn(false);

        /** @var \ACP3\Core\Helpers\Formatter\MarkEntries|PHPUnit_Framework_MockObject_MockObject $markEntriesMock */
        $markEntriesMock = $this->getMockBuilder(\ACP3\Core\Helpers\Formatter\MarkEntries::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataGrid->registerColumnRenderer(new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\HeaderColumnRenderer($markEntriesMock));
        $this->dataGrid->registerColumnRenderer(new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer());

        $this->dataGrid->addColumn([
            'label' => 'Foo',
            'fields' => ['title'],
            'type' => \ACP3\Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class
        ], 10);
        $this->dataGrid->setResults($data);

        $expected = array_merge(
            $this->getDefaultExpected(),
            [
                'header' => '<th>Foo</th>',
                'config' => [
                    'element' => '#data-grid',
                    'records_per_page' => 10,
                    'hide_col_sort' => "1",
                    'sort_col' => null,
                    'sort_dir' => null
                ],
                'results' => "<tr><td>Lorem Ipsum</td></tr>\n<tr><td>Lorem Ipsum Dolor</td></tr>\n"
            ]
        );

        $this->assertEquals($expected, $this->dataGrid->render());
    }
}