<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\Validation\Validator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractValidationRuleTestCase extends TestCase
{
    protected ValidationRuleInterface $validationRule;
    /**
     * @var Validator&MockObject
     */
    protected $validator;

    protected function setup(): void
    {
        $this->validationRule->setMessage('Invalid value.');

        $eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $container = new Container();

        $this->validator = $this
            ->getMockBuilder(Validator::class)
            ->setConstructorArgs([$eventDispatcherMock, $container])
            ->getMock();

        $container->set($this->validationRule::class, $this->validationRule);
    }

    /**
     * @return array<string, mixed[]>
     */
    abstract public static function validationRuleProvider(): array;

    /**
     * @dataProvider validationRuleProvider
     *
     * @param string[]|string|null $field
     * @param mixed[]              $extra
     */
    public function testValidationRule(mixed $data, array|string|null $field, array $extra, bool $expected): void
    {
        self::assertEquals($expected, $this->validationRule->isValid($data, $field, $extra));
    }

    /**
     * @dataProvider validationRuleProvider
     *
     * @param string[]|string|null $field
     * @param mixed[]              $extra
     */
    public function testValidate(mixed $data, array|string|null $field, array $extra, bool $expected): void
    {
        if ($expected === true) {
            $this->validator->expects(self::never())
                ->method('addError');
        } else {
            $this->validator->expects(self::once())
                ->method('addError')
                ->with('Invalid value.', $field);
        }

        $this->validationRule->validate(
            $this->validator,
            $data,
            $field,
            $extra
        );
    }
}
