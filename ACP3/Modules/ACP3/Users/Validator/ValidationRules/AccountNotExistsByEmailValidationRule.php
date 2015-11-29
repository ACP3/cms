<?php
namespace ACP3\Modules\ACP3\Users\Validator\ValidationRules;

/**
 * Class AccountsExistsByEmailValidationRule
 * @package ACP3\Modules\ACP3\Users\Validator\ValidationRules
 */
class AccountNotExistsByEmailValidationRule extends AbstractAccountNotExistsValidationRule
{
    const NAME = 'users_account_not_exists_by_email';

    /**
     * @inheritdoc
     */
    function accountExists($data, $userId)
    {
        return $this->userRepository->resultExistsByEmail($data, $userId) === false;
    }
}