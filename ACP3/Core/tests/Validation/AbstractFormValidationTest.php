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

abstract class AbstractFormValidationTest extends TestCase
{
    public const XSRF_FORM_TOKEN = 'foo-bar-baz';
    public const XSRF_QUERY_STRING = 'module/controller/action/';

    /**
     * @var \ACP3\Core\Validation\AbstractFormValidation
     */
    protected $formValidation;
    /**
     * @var MockObject|EventDispatcher
     */
    protected $eventDispatcherMock;
    /**
     * @var MockObject|Translator
     */
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

    abstract protected function initializeFormValidation(): void;

    protected function initializeFormValidationDependencies(): void
    {
        $this->translatorMock = $this->createMock(Translator::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $this->container = new Container();

        $this->validator = new Validator($this->eventDispatcherMock, $this->container);
    }

    protected function setUpFormTokenRule(): FormTokenValidationRule
    {
        return new FormTokenValidationRule(
            $this->setUpRequestMock(),
            $this->setUpSessionMock()
        );
    }

    private function setUpRequestMock(): MockObject|Request
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->setRequestMockExpectations($requestMock);

        return $requestMock;
    }

    private function setUpSessionMock(): MockObject|Session
    {
        /** @var Session|MockObject $sessionMock */
        $sessionMock = $this->createMock(Session::class);

        $this->setSessionMockExpectations($sessionMock);

        return $sessionMock;
    }

    abstract protected function registerValidationRules(): void;

    /**
     * @return array<string, mixed>[]
     */
    abstract public function validFormDataProvider(): array;

    /**
     * @return array<string, mixed>[]
     */
    abstract public function invalidFormDataProvider(): array;

    public function testValidFormData(): void
    {
        $this->expectNotToPerformAssertions();

        foreach ($this->validFormDataProvider() as $formData) {
            $this->formValidation->validate($formData);
        }
    }

    public function testInvalidFormData(): void
    {
        $this->expectException(ValidationFailedException::class);

        foreach ($this->invalidFormDataProvider() as $formData) {
            $this->formValidation->validate($formData);
        }
    }

    private function setRequestMockExpectations(MockObject $requestMock): void
    {
        $requestMock->expects(self::any())
            ->method('getPost')
            ->willReturn(
                new ParameterBag(
                    [SessionConstants::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN]
                )
            );

        $requestMock->expects(self::any())
            ->method('getQuery')
            ->willReturn(self::XSRF_QUERY_STRING);
    }

    private function setSessionMockExpectations(MockObject $sessionMock): void
    {
        $sessionMock->expects(self::any())
            ->method('get')
            ->with(SessionConstants::XSRF_TOKEN_NAME)
            ->willReturn(self::XSRF_FORM_TOKEN);
    }
}
