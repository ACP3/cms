<?php

class IntegerValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new \ACP3\Core\Validation\ValidationRules\IntegerValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-integer' => [1, '', [], true],
            'valid-data-string' => ['1', '', [], true],
            'valid-data-array-integer' => [['foo' => 1], 'foo', [], true],
            'valid-data-array-string' => [['foo' => '1'], 'foo', [], true],
            'invalid-data-string' => ['foobar', '', [], false],
            'invalid-data-float' => [0.01, '', [], false],
            'invalid-data-float-as-string' => ['0.01', '', [], false],
            'invalid-data-array' => [['foo' => 'foobar'], 'foo', [], false],
            'invalid-no-data' => [null, null, [], false]
        ];
    }
}