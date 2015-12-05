<?php

class TimeZoneExistsValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new \ACP3\Core\Validation\ValidationRules\TimeZoneExistsValidationRule();

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