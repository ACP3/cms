<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class PasswordValidationRuleTest extends AbstractValidationRuleTest
{
    protected function setup(): void
    {
        $this->validationRule = new PasswordValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-array' => [['pw' => 'test1234', 'pw_confirm' => 'test1234'], ['pw', 'pw_confirm'], [], true],
            'invalid-data-array' => [['pw' => 'test1234'], ['pw'], [], false],
            'invalid-data-flat-array' => [['test1234'], [], [], false],
            'invalid-data-string' => ['foobar', '', [], false],
            'invalid-no-data' => [null, null, [], false],
        ];
    }
}
