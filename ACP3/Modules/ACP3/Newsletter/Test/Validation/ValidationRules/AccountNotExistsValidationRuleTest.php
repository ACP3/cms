<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Test\Validation\ValidationRules;


class AccountNotExistsValidationRuleTest extends AccountExistsValidationRuleTest
{
    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-simple' => ['info@example.com', '', [], false],
            'valid-data-complex' => [['mail' => 'info@example.com'], 'mail', [], false],
            'invalid-data-simple' => ['info@example.de', '', [], true],
            'invalid-data-complex' => [['mail' => 'info@example.de'], 'mail', [], true],
        ];
    }
}
