<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class InternalUriValidationRuleTest extends AbstractValidationRuleTestCase
{
    protected function setup(): void
    {
        $this->validationRule = new InternalUriValidationRule();

        parent::setUp();
    }

    public static function validationRuleProvider(): array
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
