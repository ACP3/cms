<?php

class BirthdayValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new \ACP3\Core\Validation\ValidationRules\BirthdayValidationRule();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-string' => ['1980-03-20', '', [], true],
            'valid-data-array' => [['foo' => '1980-03-20'], 'foo', [], true],
            'invalid-data-string-with-time' => ['1980-03-20 20:00:00', '', [], false],
            'invalid-data-string-random-string' => ['abc/1234/test', '', [], false],
        ];
    }
}