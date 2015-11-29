<?php
namespace ACP3\Core\Validator\ValidationRules;

/**
 * Class InternalUriValidationRule
 * @package ACP3\Core\Validator\ValidationRules
 */
class InternalUriValidationRule extends AbstractValidationRule
{
    const NAME = 'internal_uri';

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