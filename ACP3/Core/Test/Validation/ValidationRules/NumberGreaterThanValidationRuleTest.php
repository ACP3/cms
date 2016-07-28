<?php
namespace ACP3\Core\Test\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\NumberGreaterThanValidationRule;

class NumberGreaterThanValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new NumberGreaterThanValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-string' => ['2', '', ['value' => 1], true],
            'valid-data-array' => [['foo' => '2'], 'foo', ['value' => 1], true],
            'valid-data-integer' => [2, '', ['value' => 1], true],
            'valid-data-integer-array' => [['foo' => 2], 'foo', ['value' => 1], true],
            'invalid-data-string' => ['fhdskhjk', '', ['value' => 1], false],
            'invalid-data-integer' => [20, '', ['value' => 21], false],
        ];
    }
}
