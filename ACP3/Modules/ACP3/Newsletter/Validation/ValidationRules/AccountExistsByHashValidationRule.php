<?php
namespace ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules;

/**
 * Class AccountExistsByHashValidationRule
 * @package ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules
 */
class AccountExistsByHashValidationRule extends AccountExistsValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->accountRepository->accountExistsByHash($data);
    }
}
