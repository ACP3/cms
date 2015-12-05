<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class BirthdayValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class BirthdayValidationRule extends AbstractValidationRule
{
    const NAME = 'birthday';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return !empty($data) ? $this->isBirthday($data) : true;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isBirthday($value)
    {
        $regex = '/^(\d{4})-(\d{2})-(\d{2})$/';
        $matches = [];
        if (preg_match($regex, $value, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }
        return false;
    }
}