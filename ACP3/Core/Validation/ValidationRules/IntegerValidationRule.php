<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class IntegerValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class IntegerValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkAgainstPattern($data);
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
