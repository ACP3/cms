<?php
namespace ACP3\Core\Validator\ValidationRules;

/**
 * Class IntegerValidationRule
 * @package ACP3\Core\Validator\ValidationRules
 */
class IntegerValidationRule extends AbstractValidationRule
{
    const NAME = 'integer';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->checkAgainstPattern($data[$field]);
        }

        return $this->checkAgainstPattern($field);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function checkAgainstPattern($value)
    {
        return preg_match('/^(\d+)$/', $value) === 1;
    }
}