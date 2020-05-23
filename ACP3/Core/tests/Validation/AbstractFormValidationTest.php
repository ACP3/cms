<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation;

use ACP3\Core\Http\Request;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Session\SessionConstants;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class AbstractFormValidationTest.
 */
abstract class AbstractFormValidationTest extends TestCase
{
    const XSRF_FORM_TOKEN = 'foo-bar-baz';
    const XSRF_QUERY_STRING = 'module/controller/action/';

    /**
     * @var \ACP3\Core\Validation\AbstractFormValidation
     */
    protected $formValidation;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventDispatcherMock;

    protected $translatorMock;
    /**
     * @var Validator
     */
    protected $validator;
    /**
     * @var Container
     */
    protected $container;

    protected function setup(): void
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
        $this->container = new Container();

        $this->validator = new Validator($this->eventDispatcherMock, $this->container);
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
     * @return Request&\PHPUnit\Framework\MockObject\MockObject
     */
    private function setUpRequestMock()
    {
        /** @var Request&\PHPUnit\Framework\MockObject\MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->setRequestMockExpectations($requestMock);

        return $requestMock;
    }

    /**
     * @return Session&\PHPUnit\Framework\MockObject\MockObject
     */
    private function setUpSessionMock()
    {
        /** @var Session&\PHPUnit\Framework\MockObject\MockObject $sessionMock */
        $sessionMock = $this->createMock(Session::class);

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

    private function setRequestMockExpectations(MockObject $requestMock)
    {
        $requestMock->expects($this->any())
            ->method('getPost')
            ->willReturn(
                new ParameterBag(
                    [SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN]
                )
            );

        $requestMock->expects($this->any())
            ->method('getQuery')
            ->willReturn(self::XSRF_QUERY_STRING);
    }

    private function setSessionMockExpectations(MockObject $sessionMock)
    {
        $sessionMock->expects($this->any())
            ->method('get')
            ->with(SessionConstants::XSRF_TOKEN_NAME)
            ->willReturn(self::XSRF_FORM_TOKEN);
    }
}
