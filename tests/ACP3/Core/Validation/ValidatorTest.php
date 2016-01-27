<?php

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Validation\Validator
     */
    protected $validator;

    protected function setUp()
    {
        parent::setUp();

        $this->validator = new \ACP3\Core\Validation\Validator();
    }

    public function testValidateValidValidationRuleWithValidValue()
    {
        $this->validator->registerValidationRule(new ACP3\Core\Validation\ValidationRules\EmailValidationRule());

        $this->validator->addConstraint(\ACP3\Core\Validation\ValidationRules\EmailValidationRule::class, [
            'data' => 'test@example.com',
            'field' => 'mail',
            'message' => 'Invalid E-mail address'
        ]);

        try {
            $this->validator->validate();
        } catch (\ACP3\Core\Validation\Exceptions\ValidationFailedException $e) {
            $this->fail();
        }
    }

    public function testValidateValidValidationRuleWithInvalidValue()
    {
        $this->validator->registerValidationRule(new ACP3\Core\Validation\ValidationRules\EmailValidationRule());

        $this->validator->addConstraint(\ACP3\Core\Validation\ValidationRules\EmailValidationRule::class, [
            'data' => 'testexample.com',
            'field' => 'mail',
            'message' => 'Invalid E-mail address'
        ]);

        try {
            $this->validator->validate();

            $this->fail();
        } catch (\ACP3\Core\Validation\Exceptions\ValidationFailedException $e) {
            $expected = [
                'mail' => 'Invalid E-mail address'
            ];
            $errors = unserialize($e->getMessage());

            $this->assertEquals($expected, $errors);
        }
    }

    public function testValidateValidValidationRuleWithInvalidValueWithoutFormField()
    {
        $this->validator->registerValidationRule(new ACP3\Core\Validation\ValidationRules\EmailValidationRule());

        $this->validator->addConstraint(\ACP3\Core\Validation\ValidationRules\EmailValidationRule::class, [
            'data' => 'testexample.com',
            'message' => 'Invalid E-mail address'
        ]);

        try {
            $this->validator->validate();

            $this->fail();
        } catch (\ACP3\Core\Validation\Exceptions\ValidationFailedException $e) {
            $expected = [
                'Invalid E-mail address'
            ];
            $errors = unserialize($e->getMessage());

            $this->assertEquals($expected, $errors);
        }
    }

    /**
     * @expectedException \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function testValidateInvalidValidationRule()
    {
        $this->validator->registerValidationRule(new ACP3\Core\Validation\ValidationRules\EmailValidationRule());

        $this->validator->addConstraint('invalid_validation_rule', [
            'data' => 'testexample.com',
            'message' => 'Invalid E-mail address'
        ]);

        $this->validator->validate();
    }

    /**
     * @dataProvider inlineValidationProvider
     *
     * @param string $value
     * @param bool   $expected
     */
    public function testIs($value, $expected)
    {
        $this->validator->registerValidationRule(new ACP3\Core\Validation\ValidationRules\EmailValidationRule());

        $actual = $this->validator->is(\ACP3\Core\Validation\ValidationRules\EmailValidationRule::class, $value);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function testIsInvalidValidationRule()
    {
        $this->validator->registerValidationRule(new ACP3\Core\Validation\ValidationRules\EmailValidationRule());

        $this->validator->is('invalid_validation_rule', 'test@example.com');
    }

    /**
     * @return array
     */
    public function inlineValidationProvider()
    {
        return [
            ['test@example.com', true],
            ['testexample.com', false]
        ];
    }

}