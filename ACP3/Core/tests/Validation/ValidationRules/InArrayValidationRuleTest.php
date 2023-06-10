<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class InArrayValidationRuleTest extends AbstractValidationRuleTestCase
{
    protected function setup(): void
    {
        $this->validationRule = new InArrayValidationRule();

        parent::setUp();
    }

    public static function validationRuleProvider(): array
    {
        $haystack = ['haystack' => ['foo', 'bar', 'foobar']];

        return [
            'valid-data-string' => ['foobar', '', $haystack, true],
            'valid-data-array' => [['foo' => 'foobar'], 'foo', $haystack, true],
            'invalid-data-string' => ['baz', '', $haystack, false],
            'invalid-data-array' => [['foo' => 'baz'], 'foo', $haystack, false],
            'invalid-no-data' => [null, '', [], false],
        ];
    }
}
