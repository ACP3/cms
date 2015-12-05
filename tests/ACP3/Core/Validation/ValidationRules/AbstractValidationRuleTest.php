<?php

abstract class AbstractValidationRuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Validation\ValidationRules\ValidationRuleInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $validationRule;

    /**
     * @return array
     */
    abstract public function validationRuleProvider();

    /**
     * @dataProvider validationRuleProvider
     *
     * @param mixed        $data
     * @param array|string $field
     * @param array        $extra
     * @param bool         $expected
     */
    public function testValidationRule($data, $field, $extra, $expected)
    {
        $this->assertEquals($expected, $this->validationRule->isValid($data, $field, $extra));
    }

    public function testGetName()
    {
        $this->assertNotEmpty($this->validationRule->getName());

        /** @var \ACP3\Core\Validation\ValidationRules\ValidationRuleInterface $className */
        $className = get_class($this->validationRule);
        $this->assertEquals($className::NAME, $this->validationRule->getName());
    }
}