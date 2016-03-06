<?php
namespace ACP3\Core\Test\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;

class InArrayValidationRuleTest extends AbstractValidationRuleTest
{
    protected function setUp()
    {
        $this->validationRule = new InArrayValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        $haystack = ['haystack' => ['foo', 'bar', 'foobar']];

        return [
            'valid-data-string' => ['foobar', '', $haystack, true],
            'valid-data-array' => [['foo' => 'foobar'], 'foo', $haystack, true],
            'invalid-data-string' => ['baz', '', $haystack, false],
            'invalid-data-array' => [['foo' => 'baz'], 'foo', $haystack, false],
            'invalid-no-data' => [null, null, [], false]
        ];
    }
}
