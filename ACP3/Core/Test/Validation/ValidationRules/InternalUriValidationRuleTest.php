<?php
namespace ACP3\Core\Test\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\InternalUriValidationRule;

class InternalUriValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new InternalUriValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-string' => ['abc/1234/test/', '', [], true],
            'valid-data-array' => [['foo' => 'abc/1234/test/'], 'foo', [], true],
            'invalid-data-string-two-segments' => ['abc/1234/', '', [], false],
            'invalid-data-string-missing-trailing-slash' => ['abc/1234/test', '', [], false],
            'invalid-data-string-with-uppercase-letters' => ['A12abc/1234/test/', '', [], false],
        ];
    }
}
