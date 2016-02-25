<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class NotEmptyValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class NotEmptyValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return !empty(is_array($data) ? $data : trim($data));
    }
}
