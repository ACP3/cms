<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractValidationRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \ACP3\Core\Validation\ValidationRules\ValidationRuleInterface
     */
    protected $validationRule;
    /**
     * @var \ACP3\Core\Validation\Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validator;

    protected function setUp()
    {
        $this->validationRule->setMessage('Invalid value.');

        $eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $container = new Container();

        $this->validator = $this
            ->getMockBuilder(\ACP3\Core\Validation\Validator::class)
            ->setConstructorArgs([$eventDispatcherMock, $container])
            ->getMock();

        $container->set(\get_class($this->validationRule), $this->validationRule);
    }

    /**
     * @return array
     */
    abstract public function validationRuleProvider();

    /**
     * @dataProvider validationRuleProvider
     *
     * @param mixed        $data
     * @param array|string $field
     * @param array        $extra
     * @param bool         $expected
     */
    public function testValidationRule($data, $field, $extra, $expected)
    {
        $this->assertEquals($expected, $this->validationRule->isValid($data, $field, $extra));
    }

    /**
     * @dataProvider validationRuleProvider
     *
     * @param mixed        $data
     * @param array|string $field
     * @param array        $extra
     * @param bool         $expected
     */
    public function testValidate($data, $field, $extra, $expected)
    {
        if ($expected === true) {
            $this->validator->expects($this->never())
                ->method('addError');
        } else {
            $this->validator->expects($this->once())
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
