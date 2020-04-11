<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;

class TranslateColumnRendererTest extends AbstractColumnRendererTest
{
    protected $langMock;

    protected function setUp()
    {
        $this->langMock = $this->createMock(Translator::class);

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

        $this->columnData = \array_merge($this->columnData, [
            'fields' => ['text'],
        ]);
        $this->dbData = [
            'text' => 'news',
        ];

        $expected = '<td>{NEWS_NEWS}</td>';
        $this->compareResults($expected);
    }
}
