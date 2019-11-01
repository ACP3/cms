<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Test\Validation;

use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Core\Validation\AbstractFormValidationTest;
use ACP3\Core\Validation\ValidationRules\EmailValidationRule;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountRepository;
use ACP3\Modules\ACP3\Newsletter\Validation\SubscribeFormValidation;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountNotExistsValidationRule;

class SubscribeFormValidationTest extends AbstractFormValidationTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $accountRepositoryMock;

    protected function initializeFormValidation()
    {
        $this->formValidation = new SubscribeFormValidation(
            $this->translatorMock,
            $this->validator
        );
    }

    protected function registerValidationRules()
    {
        $this->container->set(FormTokenValidationRule::class, $this->setUpFormTokenRule());
        $this->container->set(InArrayValidationRule::class, new InArrayValidationRule());
        $this->container->set(EmailValidationRule::class, new EmailValidationRule());

        $this->accountRepositoryMock = $this->createMock(AccountRepository::class);
        $this->container->set(AccountNotExistsValidationRule::class, new AccountNotExistsValidationRule($this->accountRepositoryMock));
    }

    /**
     * @return array
     */
    public function validFormDataProvider()
    {
        return [
            [
                SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => '',
                'first_name' => 'Foo',
                'last_name' => 'bar',
            ],
            [
                SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => '',
                'first_name' => '',
                'last_name' => '',
            ],
            [
                SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => '',
                'first_name' => '',
                'last_name' => '',
                'captcha' => '123456',
            ],
        ];
    }

    /**
     * @return array
     */
    public function invalidFormDataProvider()
    {
        return [
            [
                SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'testexample.com',
                'salutation' => '',
                'first_name' => 'Foo',
                'last_name' => 'bar',
            ],
            [
                SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => 3,
                'first_name' => '',
                'last_name' => '',
            ],
        ];
    }
}
