<?php
namespace ACP3\Core\Test\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\TimeZoneExistsValidationRule;

class TimeZoneExistsValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new TimeZoneExistsValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-string' => ['Europe/Berlin', '', [], true],
            'valid-data-array' => [['foo' => 'Europe/Berlin'], 'foo', [], true],
            'invalid-data-string' => ['baz', '', [], false],
            'invalid-data-array' => [['foo' => 'baz'], 'foo', [], false],
        ];
    }
}
