<?php

class ChangePasswordValidationRuleTest extends AbstractValidationRuleTest
{

    protected function setUp()
    {
        $this->validationRule = new \ACP3\Core\Validation\ValidationRules\ChangePasswordValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-array' => [['pw' => 'test1234', 'pw_confirm' => 'test1234'], ['pw', 'pw_confirm'], [], true],
            'valid-with-empty-confirm-value-data-array' => [['pw' => 'test1234', 'pw_confirm' => ''], ['pw', 'pw_confirm'], [], true],
            'valid-with-empty-values-data-array-' => [['pw' => '', 'pw_confirm' => ''], ['pw', 'pw_confirm'], [], true],
            'invalid-data-array' => [['pw' => 'test1234'], ['pw'], [], false],
            'invalid-data-flat-array' => [['test1234'], [], [], false],
            'invalid-data-string' => ['foobar', '', [], false],
            'invalid-no-data' => [null, null, [], false]
        ];
    }

}