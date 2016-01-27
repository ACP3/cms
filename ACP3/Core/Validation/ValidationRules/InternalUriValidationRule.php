<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class InternalUriValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class InternalUriValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return (bool)preg_match('/^([a-z\d_\-]+\/){3,}$/', $data);
    }
}