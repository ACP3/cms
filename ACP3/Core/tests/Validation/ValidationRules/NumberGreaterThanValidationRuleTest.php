<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class NumberGreaterThanValidationRuleTest extends AbstractValidationRuleTest
{
    protected function setup(): void
    {
        $this->validationRule = new NumberGreaterThanValidationRule();

        parent::setUp();
    }

    public function validationRuleProvider(): array
    {
        return [
            'valid-data-string' => ['2', '', ['value' => 1], true],
            'valid-data-array' => [['foo' => '2'], 'foo', ['value' => 1], true],
            'valid-data-integer' => [2, '', ['value' => 1], true],
            'valid-data-integer-array' => [['foo' => 2], 'foo', ['value' => 1], true],
            'invalid-data-string' => ['fhdskhjk', '', ['value' => 1], false],
            'invalid-data-integer' => [20, '', ['value' => 21], false],
        ];
    }
}
