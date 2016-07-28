<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class TimeZoneExistsValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class TimeZoneExistsValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        $bool = true;
        try {
            new \DateTimeZone($data);
        } catch (\Exception $e) {
            $bool = false;
        }
        return $bool;
    }
}
