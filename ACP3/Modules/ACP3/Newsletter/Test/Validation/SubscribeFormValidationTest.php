<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Test\Validation;

use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Core\Test\Validation\AbstractFormValidationTest;
use ACP3\Core\Validation\ValidationRules\EmailValidationRule;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterAccountsRepository;
use ACP3\Modules\ACP3\Newsletter\Validation\SubscribeFormValidation;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountNotExistsValidationRule;

class SubscribeFormValidationTest extends AbstractFormValidationTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $accountRepositoryMock;

    /**
     * @return void
     */
    protected function initializeFormValidation()
    {
        $this->formValidation = new SubscribeFormValidation(
            $this->translatorMock,
            $this->validator
        );
    }

    /**
     * @return void
     */
    protected function registerValidationRules()
    {
        $this->validator->registerValidationRule($this->setUpFormTokenRule());

        $inArrayRule = new InArrayValidationRule();
        $this->validator->registerValidationRule($inArrayRule);

        $emailRule = new EmailValidationRule();
        $this->validator->registerValidationRule($emailRule);

        $this->accountRepositoryMock = $this->getMockBuilder(NewsletterAccountsRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $accountNotExistsRule = new AccountNotExistsValidationRule($this->accountRepositoryMock);
        $this->validator->registerValidationRule($accountNotExistsRule);
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
