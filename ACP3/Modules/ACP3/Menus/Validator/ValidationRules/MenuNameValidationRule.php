<?php
namespace ACP3\Modules\ACP3\Menus\Validator\ValidationRules;

use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;

/**
 * Class MenuNameValidationRule
 * @package ACP3\Modules\ACP3\Menus\Validator\ValidationRules
 */
class MenuNameValidationRule extends AbstractValidationRule
{
    const NAME = 'menus_menu_name';

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