<?php

class UriSafeValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new \ACP3\Core\Validation\ValidationRules\UriSafeValidationRule();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-string' => ['abc/1234', '', [], true],
            'invalid-data-string-with-umlauts' => ['abüöc/1234', '', [], false],
            'invalid-data-string-with-beginning-number' => ['12abc/1234', '', [], false],
            'invalid-data-string-with-uppercase-letters' => ['A12abc/1234', '', [], false],
        ];
    }
}
