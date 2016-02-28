<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

/**
 * Class AbstractFormValidationTest
 */
abstract class AbstractFormValidationTest extends \PHPUnit_Framework_TestCase
{
    const XSRF_FORM_TOKEN = 'foo-bar-baz';
    const XSRF_QUERY_STRING = 'module/controller/action/';

    /**
     * @var \ACP3\Core\Validation\AbstractFormValidation
     */
    protected $formValidation;
    /**
     * @var \ACP3\Core\I18n\Translator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;
    /**
     * @var \ACP3\Core\Validation\Validator
     */
    protected $validator;

    protected function setUp()
    {
        $this->initializeFormValidationDependencies();
        $this->initializeFormValidation();
        $this->registerValidationRules();
    }

    /**
     * @return void
     */
    abstract protected function initializeFormValidation();

    protected function initializeFormValidationDependencies()
    {
        $this->translatorMock = $this->getMockBuilder(\ACP3\Core\I18n\Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['t'])
            ->getMock();
        $this->validator = new \ACP3\Core\Validation\Validator();
    }

    /**
     * @return \ACP3\Core\Validation\ValidationRules\FormTokenValidationRule
     */
    protected function setUpFormTokenRule()
    {
        return new \ACP3\Core\Validation\ValidationRules\FormTokenValidationRule(
            $this->setUpRequestMock(),
            $this->setUpSessionMock()
        );
    }

    /**
     * @return \ACP3\Core\Http\Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private function setUpRequestMock()
    {
        /** @var \ACP3\Core\Http\Request|PHPUnit_Framework_MockObject_MockObject $requestMock */
        $requestMock = $this->getMockBuilder(\ACP3\Core\Http\Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPost', 'getQuery'])
            ->getMock();

        $this->setRequestMockExpections($requestMock);

        return $requestMock;
    }

    /**
     * @return \ACP3\Core\SessionHandler|\PHPUnit_Framework_MockObject_MockObject
     */
    private function setUpSessionMock()
    {
        /** @var \ACP3\Core\SessionHandler|PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder(\ACP3\Core\SessionHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $this->setSessionMockExpections($sessionMock);

        return $sessionMock;
    }

    /**
     * @return void
     */
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
        foreach ($this->validFormDataProvider() as $formData) {
            $this->formValidation->validate($formData);
        }
    }

    public function testInvalidFormData()
    {
        $this->setExpectedException(\ACP3\Core\Validation\Exceptions\ValidationFailedException::class);

        foreach ($this->invalidFormDataProvider() as $formData) {
            $this->formValidation->validate($formData);
        }
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $requestMock
     */
    private function setRequestMockExpections(PHPUnit_Framework_MockObject_MockObject$requestMock)
    {
        $requestMock->expects($this->any())
            ->method('getPost')
            ->willReturn(new \ACP3\Core\Http\Request\ParameterBag(
                    [\ACP3\Core\SessionHandler::XSRF_TOKEN_NAME => self::XSRF_FORM_TOKEN])
            );

        $requestMock->expects($this->any())
            ->method('getQuery')
            ->willReturn(self::XSRF_QUERY_STRING);
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $sessionMock
     */
    private function setSessionMockExpections(PHPUnit_Framework_MockObject_MockObject $sessionMock)
    {
        $sessionMock->expects($this->any())
            ->method('get')
            ->with(\ACP3\Core\SessionHandler::XSRF_TOKEN_NAME)
            ->willReturn([self::XSRF_QUERY_STRING => self::XSRF_FORM_TOKEN]);
    }
}
