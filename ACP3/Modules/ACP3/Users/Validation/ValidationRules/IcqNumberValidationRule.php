<?php
namespace ACP3\Modules\ACP3\Users\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

/**
 * Class IcqNumberValidationRule
 * @package ACP3\Modules\ACP3\Users\Validation\ValidationRules
 */
class IcqNumberValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return !empty($data) ? $this->isIcqNumber($data) : true;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isIcqNumber($value)
    {
        return (bool)preg_match('/^(\d{6,9})$/', $value);
    }
}
