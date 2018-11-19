<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Validation;

use ACP3\Core\Http\Request;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Session\SessionHandler;
use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use ACP3\Core\Validation\Validator;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class AbstractFormValidationTest.
 */
abstract class AbstractFormValidationTest extends \PHPUnit\Framework\TestCase
{
    const XSRF_FORM_TOKEN = 'foo-bar-baz';
    const XSRF_QUERY_STRING = 'module/controller/action/';

    /**
     * @var \ACP3\Core\Validation\AbstractFormValidation
     */
    protected $formValidation;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcherMock;
    /**
     * @var Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;
    /**
     * @var Validator
     */
    protected $validator;

    protected function setUp()
    {
        $this->initializeFormValidationDependencies();
        $this->initializeFormValidation();
        $this->registerValidationRules();
    }

    abstract protected function initializeFormValidation();

    protected function initializeFormValidationDependencies()
    {
        $this->translatorMock = $this->createMock(Translator::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcher::class);

        $this->validator = new Validator($this->eventDispatcherMock);
    }

    /**
     * @return FormTokenValidationRule
     */
    protected function setUpFormTokenRule()
    {
        return new FormTokenValidationRule(
            $this->setUpRequestMock(),
            $this->setUpSessionMock()
        );
    }

    /**
     * @return Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private function setUpRequestMock()
    {
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->setRequestMockExpectations($requestMock);

        return $requestMock;
    }

    /**
     * @return SessionHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    private function setUpSessionMock()
    {
        /** @var SessionHandler|\PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->createMock(SessionHandler::class);

        $this->setSessionMockExpectations($sessionMock);

        return $sessionMock;
    }

    abstract protected function registerValidationRules();

    /**
     * @return array
     */
    abstract public function validFormDataProvider();

    /**
     * @return array
     */
    abstract public function invalidFormDataProvider();

    public function testValidFormData()
    {
        $this->expectNotToPerformAssertions();

        foreach ($this->validFormDataProvider() as $formData) {
            $this->formValidation->validate($formData);
        }
    }

    public function testInvalidFormData()
    {
        $this->expectException(ValidationFailedException::class);

        foreach ($this->invalidFormDataProvider() as $formData) {
            $this->formValidation->validate($formData);
        }
    }

    private function setRequestMockExpectations(\PHPUnit\Framework\MockObject\MockObject $requestMock)
    {
        $requestMock->expects($this->any())
            ->method('getPost')
            ->willReturn(
                new \Symfony\Component\HttpFoundation\ParameterBag(
                    [SessionHandlerInterface::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN]
                )
            );

        $requestMock->expects($this->any())
            ->method('getQuery')
            ->willReturn(self::XSRF_QUERY_STRING);
    }

    private function setSessionMockExpectations(\PHPUnit\Framework\MockObject\MockObject $sessionMock)
    {
        $sessionMock->expects($this->any())
            ->method('get')
            ->with(SessionHandlerInterface::XSRF_TOKEN_NAME)
            ->willReturn(self::XSRF_FORM_TOKEN);
    }
}
