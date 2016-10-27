<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Test\Validation;


use ACP3\Core\ACL;
use ACP3\Core\Http\Request;
use ACP3\Core\Router\Router;
use ACP3\Core\Session\SessionHandler;
use ACP3\Core\Test\Validation\AbstractFormValidationTest;
use ACP3\Core\Validation\ValidationRules\EmailValidationRule;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\AccountRepository;
use ACP3\Modules\ACP3\Newsletter\Validation\SubscribeFormValidation;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountNotExistsValidationRule;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class SubscribeFormValidationTest extends AbstractFormValidationTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $aclMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $accountRepositoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $routerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionHandlerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $userModelMock;

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

        $this->accountRepositoryMock = $this->getMockBuilder(AccountRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $accountNotExistsRule = new AccountNotExistsValidationRule($this->accountRepositoryMock);
        $this->validator->registerValidationRule($accountNotExistsRule);

        $this->aclMock = $this->getMockBuilder(ACL::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sessionHandlerMock = $this->getMockBuilder(SessionHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userModelMock = $this->getMockBuilder(UserModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $captchaRule = new CaptchaValidationRule(
            $this->aclMock,
            $this->requestMock,
            $this->routerMock,
            $this->sessionHandlerMock,
            $this->userModelMock
        );
        $this->validator->registerValidationRule($captchaRule);
    }

    /**
     * @return array
     */
    public function validFormDataProvider()
    {
        return [
            [
                \ACP3\Core\Session\SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => '',
                'first_name' => 'Foo',
                'last_name' => 'bar'
            ],
            [
                \ACP3\Core\Session\SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => '',
                'first_name' => '',
                'last_name' => ''
            ],
            [
                \ACP3\Core\Session\SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => '',
                'first_name' => '',
                'last_name' => '',
                'captcha' => '123456'
            ]
        ];
    }

    /**
     * @return array
     */
    public function invalidFormDataProvider()
    {
        return [
            [
                \ACP3\Core\Session\SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'testexample.com',
                'salutation' => '',
                'first_name' => 'Foo',
                'last_name' => 'bar'
            ],
            [
                \ACP3\Core\Session\SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN,
                'mail' => 'test@example.com',
                'salutation' => 3,
                'first_name' => '',
                'last_name' => ''
            ]
        ];
    }
}
