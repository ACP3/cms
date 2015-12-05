<?php

class EmailValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new \ACP3\Core\Validation\ValidationRules\EmailValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-string' => ['test@example.com', '', [], true],
            'valid-data-array' => [['foo' => 'test@example.com'], 'foo', [], true],
            'valid-email-with-subdomain' => ['test@subdomain.example.com', '', [], true],
            'invalid-data-string' => ['foobar', '', [], false],
            'invalid-data-array' => [['foo' => 'foobar'], 'foo', [], false],
            'invalid-no-data' => [null, null, [], false]
        ];
    }
}