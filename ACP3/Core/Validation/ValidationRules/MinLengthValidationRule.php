<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class MinLengthValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class MinLengthValidationRule extends AbstractValidationRule
{
    const NAME = 'min_length';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkMinLength($data, $extra['length']);
    }

    /**
     * @param string  $value
     * @param integer $length
     *
     * @return bool
     */
    protected function checkMinLength($value, $length)
    {
        return mb_strlen(trim($value)) >= $length;
    }
}