<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class IntegerValidationRuleTest extends AbstractValidationRuleTest
{
    protected function setup(): void
    {
        $this->validationRule = new IntegerValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-integer' => [1, '', [], true],
            'valid-data-string' => ['1', '', [], true],
            'valid-data-array-integer' => [['foo' => 1], 'foo', [], true],
            'valid-data-array-string' => [['foo' => '1'], 'foo', [], true],
            'invalid-data-string' => ['foobar', '', [], false],
            'invalid-data-float' => [0.01, '', [], false],
            'invalid-data-float-as-string' => ['0.01', '', [], false],
            'invalid-data-array' => [['foo' => 'foobar'], 'foo', [], false],
            'invalid-no-data' => [null, null, [], false],
        ];
    }
}
