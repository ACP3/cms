<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class BirthdayValidationRuleTest extends AbstractValidationRuleTest
{
    protected function setup(): void
    {
        $this->validationRule = new BirthdayValidationRule();

        parent::setUp();
    }

    public function validationRuleProvider(): array
    {
        return [
            'valid-data-string' => ['1980-03-20', '', [], true],
            'valid-data-array' => [['foo' => '1980-03-20'], 'foo', [], true],
            'invalid-data-string-with-time' => ['1980-03-20 20:00:00', '', [], false],
            'invalid-data-string-random-string' => ['abc/1234/test', '', [], false],
        ];
    }
}
