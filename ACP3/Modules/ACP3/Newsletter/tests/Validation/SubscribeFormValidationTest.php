<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Validation;

use ACP3\Core\Session\SessionConstants;
use ACP3\Core\Validation\AbstractFormValidationTest;
use ACP3\Core\Validation\ValidationRules\EmailValidationRule;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Modules\ACP3\Newsletter\Repository\AccountRepository;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountNotExistsValidationRule;

class SubscribeFormValidationTest extends AbstractFormValidationTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&AccountRepository
     */
    private $accountRepositoryMock;

    protected function initializeFormValidation(): void
    {
        $this->formValidation = new SubscribeFormValidation(
            $this->translatorMock,
            $this->validator
        );
    }

    protected function registerValidationRules(): void
    {
        $this->container->set(FormTokenValidationRule::class, $this->setUpFormTokenRule());
        $this->container->set(InArrayValidationRule::class, new InArrayValidationRule());
        $this->container->set(EmailValidationRule::class, new EmailValidationRule());

        $this->accountRepositoryMock = $this->createMock(AccountRepository::class);
        $this->accountRepositoryMock->method('accountExists')
            ->willReturn(false);
        $this->container->set(AccountNotExistsValidationRule::class, new AccountNotExistsValidationRule($this->accountRepositoryMock));
    }

    /**
     * {@inheritDoc}
     */
    public function validFormDataProvider(): array
    {
        return [
            'with-names' => [
                SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => '',
                'first_name' => 'Foo',
                'last_name' => 'bar',
            ],
            'without-names' => [
                SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => '',
                'first_name' => '',
                'last_name' => '',
            ],
            'with-captcha' => [
                SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => '',
                'first_name' => '',
                'last_name' => '',
                'captcha' => '123456',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function invalidFormDataProvider(): array
    {
        return [
            [
                SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'testexample.com',
                'salutation' => '',
                'first_name' => 'Foo',
                'last_name' => 'bar',
            ],
            [
                SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => 3,
                'first_name' => '',
                'last_name' => '',
            ],
        ];
    }
}
