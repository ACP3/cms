<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Validation;

use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\Validation\ValidationRules\EmailValidationRule;
use ACP3\Core\Validation\Validator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcherMock;
    /**
     * @var Validator
     */
    protected $validator;
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $this->container = new Container();

        $this->validator = new Validator($this->eventDispatcherMock, $this->container);
    }

    public function testValidateValidValidationRuleWithValidValue()
    {
        $emailValidationRuleMock = $this->createMock(EmailValidationRule::class);

        $emailValidationRuleMock->expects($this->once())
            ->method('validate')
            ->with($this->validator, 'test@example.com', 'mail', []);

        $this->container->set(EmailValidationRule::class, $emailValidationRuleMock);

        $this->validator->addConstraint(EmailValidationRule::class, [
            'data' => 'test@example.com',
            'field' => 'mail',
            'message' => 'Invalid E-mail address',
        ]);

        try {
            $this->validator->validate();
        } catch (ValidationFailedException $e) {
            $this->fail();
        }
    }

    public function testValidateValidValidationRuleWithInvalidValue()
    {
        $this->container->set(EmailValidationRule::class, new EmailValidationRule());

        $this->validator->addConstraint(EmailValidationRule::class, [
            'data' => 'testexample.com',
            'field' => 'mail',
            'message' => 'Invalid E-mail address',
        ]);

        try {
            $this->validator->validate();

            $this->fail();
        } catch (ValidationFailedException $e) {
            $expected = [
                'mail' => 'Invalid E-mail address',
            ];
            $errors = \unserialize($e->getMessage());

            $this->assertEquals($expected, $errors);
        }
    }

    public function testValidateValidValidationRuleWithInvalidValueWithoutFormField()
    {
        $this->container->set(EmailValidationRule::class, new EmailValidationRule());

        $this->validator->addConstraint(EmailValidationRule::class, [
            'data' => 'testexample.com',
            'message' => 'Invalid E-mail address',
        ]);

        try {
            $this->validator->validate();

            $this->fail();
        } catch (ValidationFailedException $e) {
            $expected = [
                'Invalid E-mail address',
            ];
            $errors = \unserialize($e->getMessage());

            $this->assertEquals($expected, $errors);
        }
    }

    public function testValidateInvalidValidationRule()
    {
        $this->expectException(\ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException::class);

        $this->container->set(EmailValidationRule::class, new EmailValidationRule());

        $this->validator->addConstraint('invalid_validation_rule', [
            'data' => 'testexample.com',
            'message' => 'Invalid E-mail address',
        ]);

        $this->validator->validate();
    }

    /**
     * @dataProvider inlineValidationProvider
     *
     * @param string $value
     * @param bool   $expected
     *
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function testIs($value, $expected)
    {
        $this->container->set(EmailValidationRule::class, new EmailValidationRule());

        $actual = $this->validator->is(EmailValidationRule::class, $value);
        $this->assertEquals($expected, $actual);
    }

    public function testIsInvalidValidationRule()
    {
        $this->expectException(\ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException::class);

        $this->container->set(EmailValidationRule::class, new EmailValidationRule());

        $this->validator->is('invalid_validation_rule', 'test@example.com');
    }

    /**
     * @return array
     */
    public function inlineValidationProvider()
    {
        return [
            ['test@example.com', true],
            ['testexample.com', false],
        ];
    }
}
