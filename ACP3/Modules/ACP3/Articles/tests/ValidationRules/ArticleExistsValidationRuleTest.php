<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRuleTest;
use ACP3\Modules\ACP3\Articles\Repository\ArticleRepository;
use ACP3\Modules\ACP3\Articles\Validation\ValidationRules\ArticleExistsValidationRule;

class ArticleExistsValidationRuleTest extends AbstractValidationRuleTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $articlesRepositoryMock;

    protected function setup(): void
    {
        $this->articlesRepositoryMock = $this->createMock(ArticleRepository::class);

        $this->validationRule = new ArticleExistsValidationRule($this->articlesRepositoryMock);

        parent::setUp();
    }

    public function validationRuleProvider(): array
    {
        return [
            'valid-data-simple' => [1, '', [], true],
            'valid-data-complex' => [['article_id' => 1], 'article_id', [], true],
            'invalid-data-simple' => [5, '', [], false],
            'invalid-data-complex' => [['article_id' => 5], 'article_id', [], false],
        ];
    }

    /**
     * @dataProvider validationRuleProvider
     *
     * @param mixed        $data
     * @param array|string $field
     */
    public function testValidationRule($data, $field, array $extra, bool $expected): void
    {
        $this->setExpectations($expected);

        parent::testValidationRule($data, $field, $extra, $expected);
    }

    /**
     * @param bool $expected
     */
    private function setExpectations($expected)
    {
        $this->articlesRepositoryMock
            ->expects(self::once())
            ->method('resultExists')
            ->willReturn($expected);
    }

    /**
     * @dataProvider validationRuleProvider
     *
     * @param mixed        $data
     * @param array|string $field
     */
    public function testValidate($data, $field, array $extra, bool $expected): void
    {
        $this->setExpectations($expected);

        parent::testValidate($data, $field, $extra, $expected);
    }
}
