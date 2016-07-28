<?php
namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

/**
 * Class MenuNameValidationRule
 * @package ACP3\Modules\ACP3\Menus\Validation\ValidationRules
 */
class MenuNameValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return preg_match('/^[a-zA-Z]+\w/', $data) === 1;
    }
}
