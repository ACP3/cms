<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation;

use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException;
use ACP3\Core\Validation\ValidationRules\EmailValidationRule;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    private EventDispatcher&MockObject $eventDispatcherMock;
    private Validator $validator;
    private Container $container;

    protected function setup(): void
    {
        parent::setUp();

        $this->eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $this->container = new Container();

        $this->validator = new Validator($this->eventDispatcherMock, $this->container);
    }

    public function testValidateValidValidationRuleWithValidValue(): void
    {
        $emailValidationRuleMock = $this->createMock(EmailValidationRule::class);

        $emailValidationRuleMock->expects(self::once())
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
        } catch (ValidationFailedException) {
            $this->fail();
        }
    }

    public function testValidateValidValidationRuleWithInvalidValue(): void
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
            $errors = unserialize($e->getMessage());

            self::assertEquals($expected, $errors);
        }
    }

    public function testValidateValidValidationRuleWithInvalidValueWithoutFormField(): void
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
            $errors = unserialize($e->getMessage());

            self::assertEquals($expected, $errors);
        }
    }

    public function testValidateInvalidValidationRule(): void
    {
        $this->expectException(ValidationRuleNotFoundException::class);

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
     * @throws ValidationRuleNotFoundException
     */
    public function testIs(string $value, bool $expected): void
    {
        $this->container->set(EmailValidationRule::class, new EmailValidationRule());

        $actual = $this->validator->is(EmailValidationRule::class, $value);
        self::assertEquals($expected, $actual);
    }

    public function testIsInvalidValidationRule(): void
    {
        $this->expectException(ValidationRuleNotFoundException::class);

        $this->container->set(EmailValidationRule::class, new EmailValidationRule());

        /* @phpstan-ignore-next-line */
        $this->validator->is('invalid_validation_rule', 'test@example.com');
    }

    /**
     * @return mixed[]
     */
    public function inlineValidationProvider(): array
    {
        return [
            ['test@example.com', true],
            ['testexample.com', false],
        ];
    }

    public function testDispatchValidationEventWithEventName(): void
    {
        $formData = ['foo' => 'bar'];
        $extra = ['extra' => true];

        $this->eventDispatcherMock->expects(self::once())
            ->method('dispatch')
            ->with(new FormValidationEvent($this->validator, $formData, $extra), 'foo.form_validation_event');

        $this->validator->dispatchValidationEvent('foo.form_validation_event', $formData, $extra);
    }

    public function testDispatchValidationEventWithClassStringAsEventName(): void
    {
        $formData = ['foo' => 'bar'];
        $extra = ['extra' => true];

        $this->eventDispatcherMock->expects(self::once())
            ->method('dispatch')
            ->with(new FormValidationEvent($this->validator, $formData, $extra));

        $this->validator->dispatchValidationEvent(FormValidationEvent::class, $formData, $extra);
    }
}
