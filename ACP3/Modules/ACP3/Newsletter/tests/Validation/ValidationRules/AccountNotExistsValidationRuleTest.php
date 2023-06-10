<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules;

class AccountNotExistsValidationRuleTest extends AccountExistsValidationRuleTest
{
    public static function validationRuleProvider(): array
    {
        return [
            'valid-data-simple' => ['info@example.com', '', [], false],
            'valid-data-complex' => [['mail' => 'info@example.com'], 'mail', [], false],
            'invalid-data-simple' => ['info@example.de', '', [], true],
            'invalid-data-complex' => [['mail' => 'info@example.de'], 'mail', [], true],
        ];
    }
}
