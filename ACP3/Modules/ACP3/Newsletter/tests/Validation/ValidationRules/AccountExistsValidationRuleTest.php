<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRuleTest;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountRepository;

class AccountExistsValidationRuleTest extends AbstractValidationRuleTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $accountRepositoryMock;

    protected function setup(): void
    {
        $this->accountRepositoryMock = $this->createMock(AccountRepository::class);

        $this->validationRule = new AccountExistsValidationRule($this->accountRepositoryMock);

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-simple' => ['info@example.com', '', [], true],
            'valid-data-complex' => [['mail' => 'info@example.com'], 'mail', [], true],
            'invalid-data-simple' => ['info@example.de', '', [], false],
            'invalid-data-complex' => [['mail' => 'info@example.de'], 'mail', [], false],
        ];
    }

    /**
     * @dataProvider validationRuleProvider
     *
     * @param mixed        $data
     * @param array|string $field
     * @param array        $extra
     * @param bool         $expected
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
        $this->accountRepositoryMock
            ->expects(self::once())
            ->method('accountExists')
            ->willReturn($expected);
    }

    /**
     * @dataProvider validationRuleProvider
     *
     * @param mixed        $data
     * @param array|string $field
     * @param array        $extra
     * @param bool         $expected
     */
    public function testValidate($data, $field, $extra, $expected)
    {
        $this->setExpectations($expected);

        parent::testValidate($data, $field, $extra, $expected);
    }
}
