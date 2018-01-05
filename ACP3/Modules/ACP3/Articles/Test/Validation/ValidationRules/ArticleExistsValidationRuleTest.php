<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Test\Validation\ValidationRules;

use ACP3\Core\Test\Validation\ValidationRules\AbstractValidationRuleTest;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticlesRepository;
use ACP3\Modules\ACP3\Articles\Validation\ValidationRules\ArticleExistsValidationRule;

class ArticleExistsValidationRuleTest extends AbstractValidationRuleTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $articlesRepositoryMock;

    protected function setUp()
    {
        $this->articlesRepositoryMock = $this->getMockBuilder(ArticlesRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['resultExists'])
            ->getMock();

        $this->validationRule = new ArticleExistsValidationRule($this->articlesRepositoryMock);

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
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
     * @param mixed $data
     * @param array|string $field
     * @param array $extra
     * @param bool $expected
     */
    public function testValidationRule($data, $field, $extra, $expected)
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
            ->expects($this->once())
            ->method('resultExists')
            ->willReturn($expected);
    }

    /**
     * @dataProvider validationRuleProvider
     *
     * @param mixed $data
     * @param array|string $field
     * @param array $extra
     * @param bool $expected
     */
    public function testValidate($data, $field, $extra, $expected)
    {
        $this->setExpectations($expected);

        parent::testValidate($data, $field, $extra, $expected);
    }
}
