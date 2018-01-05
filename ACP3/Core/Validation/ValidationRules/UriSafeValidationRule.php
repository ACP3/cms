<?php
namespace ACP3\Core\Validation\ValidationRules;

class UriSafeValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        if (\is_scalar($data)) {
            return (bool)\preg_match('/^([a-z]{1}[a-z\d\-]*(\/[a-z\d\-]+)*)$/', $data);
        }

        return false;
    }
}
