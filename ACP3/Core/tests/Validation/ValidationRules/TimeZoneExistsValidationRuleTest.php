<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class TimeZoneExistsValidationRuleTest extends AbstractValidationRuleTestCase
{
    protected function setup(): void
    {
        $this->validationRule = new TimeZoneExistsValidationRule();

        parent::setUp();
    }

    public static function validationRuleProvider(): array
    {
        return [
            'valid-data-string' => ['Europe/Berlin', '', [], true],
            'valid-data-array' => [['foo' => 'Europe/Berlin'], 'foo', [], true],
            'invalid-data-string' => ['baz', '', [], false],
            'invalid-data-array' => [['foo' => 'baz'], 'foo', [], false],
        ];
    }
}
