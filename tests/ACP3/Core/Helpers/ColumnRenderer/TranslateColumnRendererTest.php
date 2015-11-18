<?php

class TranslateColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var \ACP3\Core\Lang|PHPUnit_Framework_MockObject_MockObject
     */
    protected  $langMock;

    protected function setUp()
    {
        $this->langMock = $this->getMockBuilder(\ACP3\Core\Lang::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();

        $this->columnRenderer = new \ACP3\Core\Helpers\DataGrid\ColumnRenderer\TranslateColumnRenderer($this->langMock);

        parent::setUp();
    }

    /**
     * @param string $langKey
     * @param string $langValue
     */
    private function setUpLangMockExpectation($langKey, $langValue)
    {
        $this->langMock->expects($this->once())
            ->method('t')
            ->with($langKey, $langKey)
            ->willReturn($langValue);

    }

    public function testValidField()
    {
        $this->setUpLangMockExpectation('news', '{NEWS_NEWS}');

        $this->columnData = array_merge($this->columnData, [
            'fields' => ['text']
        ]);
        $this->dbData = [
            'text' => 'news'
        ];

        $expected = '<td>{NEWS_NEWS}</td>';
        $this->compareResults($expected);
    }
}