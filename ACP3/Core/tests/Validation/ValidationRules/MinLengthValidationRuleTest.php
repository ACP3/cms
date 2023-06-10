<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class MinLengthValidationRuleTest extends AbstractValidationRuleTestCase
{
    protected function setup(): void
    {
        $this->validationRule = new MinLengthValidationRule();

        parent::setUp();
    }

    public static function validationRuleProvider(): array
    {
        return [
            'valid-data-string' => ['foobar', '', ['length' => 3], true],
            'valid-data-array' => [['foo' => 'foobar'], 'foo', ['length' => 3], true],
            'invalid-data-string' => ['foobar', '', ['length' => 7], false],
            'invalid-data-array' => [['foo' => 'foobar'], 'foo', ['length' => 7], false],
            'invalid-no-data' => [null, '', [], false],
        ];
    }
}
