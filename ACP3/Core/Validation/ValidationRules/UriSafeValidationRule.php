<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class UriSafeValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class UriSafeValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return (bool)preg_match('/^([a-z]{1}[a-z\d\-]*(\/[a-z\d\-]+)*)$/', $data);
    }
}
