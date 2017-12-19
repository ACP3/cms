<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Test\ValidationRules;

use ACP3\Core\Test\Validation\ValidationRules\AbstractValidationRuleTest;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;
use ACP3\Modules\ACP3\Articles\Validation\ValidationRules\ArticleExistsValidationRule;

class ArticleExistsValidationRuleTest extends AbstractValidationRuleTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $articlesRepositoryMock;

    protected function setUp()
    {
        $this->articlesRepositoryMock = $this->getMockBuilder(ArticleRepository::class)
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