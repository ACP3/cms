<?php
namespace ACP3\Core\Test\Helpers\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\TranslateColumnRenderer;
use ACP3\Core\I18n\Translator;

class TranslateColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected  $langMock;

    protected function setUp()
    {
        $this->langMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();

        $this->columnRenderer = new TranslateColumnRenderer($this->langMock);

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
