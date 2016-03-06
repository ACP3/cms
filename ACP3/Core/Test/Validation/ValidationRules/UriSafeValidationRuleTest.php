<?php
namespace ACP3\Core\Test\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\UriSafeValidationRule;

class UriSafeValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new UriSafeValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-string' => ['abc/1234', '', [], true],
            'valid-data-array' => [['foo' => 'abc/1234'], 'foo', [], true],
            'invalid-data-string-with-umlauts' => ['abüöc/1234', '', [], false],
            'invalid-data-string-with-beginning-number' => ['12abc/1234', '', [], false],
            'invalid-data-string-with-uppercase-letters' => ['A12abc/1234', '', [], false],
        ];
    }
}
