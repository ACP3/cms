<?php

class NotEmptyValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new \ACP3\Core\Validation\ValidationRules\NotEmptyValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-string' => ['foobar', '', [], true],
            'valid-data-array' => [['foo' => 'bar'], 'foo', [], true],
            'valid-data-not-empty-array' => [['foo' => ['foobar']], 'foo', [], true],
            'valid-data-not-empty-array-two' => [['foobar'], '', [], true],
            'invalid-data-string' => ['', '', [], false],
            'invalid-data-string-whitespace' => ['    ', '', [], false],
            'invalid-data-string-whitespaces-newlines' => ["    \r\n", '', [], false],
            'invalid-data-empty-array' => [['foo' => []], 'foo', [], false],
            'invalid-data-empty-array-2' => [[], '', [], false],
            'invalid-data-array' => [['foo' => ''], 'foo', [], false],
            'invalid-data-array-whitespace' => [['foo' => '    '], 'foo', [], false],
            'invalid-data-array-whitespaces-newlines' => [['foo' => "    \r\n"], 'foo', [], false],
        ];
    }
}
