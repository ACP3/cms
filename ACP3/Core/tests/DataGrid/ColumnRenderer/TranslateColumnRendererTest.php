<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;
use PHPUnit\Framework\MockObject\MockObject;

class TranslateColumnRendererTest extends AbstractColumnRendererTest
{
    /**
     * @var Translator|MockObject
     */
    private $langMock;

    protected function setup(): void
    {
        $this->langMock = $this->createMock(Translator::class);

        $this->columnRenderer = new TranslateColumnRenderer($this->langMock);

        parent::setUp();
    }

    private function setUpLangMockExpectation(string $langKey, string $langValue): void
    {
        $this->langMock->expects(self::once())
            ->method('t')
            ->with($langKey, $langKey)
            ->willReturn($langValue);
    }

    public function testValidField(): void
    {
        $this->setUpLangMockExpectation('news', '{NEWS_NEWS}');

        $this->columnData = [...$this->columnData, ...[
            'fields' => ['text'],
        ]];
        $this->dbData = [
            'text' => 'news',
        ];

        $expected = '<td>{NEWS_NEWS}</td>';
        $this->compareResults($expected);
    }
}
