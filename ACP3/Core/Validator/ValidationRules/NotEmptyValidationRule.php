<?php
namespace ACP3\Core\Validator\ValidationRules;

/**
 * Class NotEmptyValidationRule
 * @package ACP3\Core\Validator\ValidationRules
 */
class NotEmptyValidationRule extends AbstractValidationRule
{
    const NAME = 'not_empty';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return !empty($data);
    }
}