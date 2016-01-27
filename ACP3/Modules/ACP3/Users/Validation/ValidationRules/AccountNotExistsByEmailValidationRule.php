<?php
namespace ACP3\Modules\ACP3\Users\Validation\ValidationRules;

/**
 * Class AccountsExistsByEmailValidationRule
 * @package ACP3\Modules\ACP3\Users\Validation\ValidationRules
 */
class AccountNotExistsByEmailValidationRule extends AbstractAccountNotExistsValidationRule
{
    /**
     * @inheritdoc
     */
    function accountExists($data, $userId)
    {
        return $this->userRepository->resultExistsByEmail($data, $userId) === false;
    }
}