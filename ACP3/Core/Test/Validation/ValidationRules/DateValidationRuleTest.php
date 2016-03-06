<?php
namespace ACP3\Core\Test\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\DateValidationRule;

class DateValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new DateValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-string' => ['1980-03-20', '', [], true],
            'valid-data-string-with-time' => ['1980-03-20 20:05', '', [], true],
            'valid-data-array' => [['foo' => '1980-03-20'], 'foo', [], true],
            'valid-data-array-with-time' => [['foo' => '1980-03-20 20:05'], 'foo', [], true],
            'valid-data-array-range' => [['start' => '1980-03-20', 'end' => '1980-03-21'], ['start', 'end'], [], true],
            'valid-data-array-range-with-time' => [['start' => '1980-03-20 20:05', 'end' => '1980-03-21 20:05'], ['start', 'end'], [], true],
            'invalid-data-string-random-string' => ['abc/1234/test', '', [], false],
            'invalid-data-array-range' => [['start' => '1980-03-20', 'end' => '1980-03-19'], ['start', 'end'], [], false],
        ];
    }
}
