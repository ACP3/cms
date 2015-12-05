<?php
namespace ACP3\Modules\ACP3\Users\Validation\ValidationRules;

/**
 * Class AccountNotNotExistsByNameValidationRule
 * @package ACP3\Modules\ACP3\Users\Validation\ValidationRules
 */
class AccountNotExistsByNameValidationRule extends AbstractAccountNotExistsValidationRule
{
    const NAME = 'users_account_not_exists_by_name';

    /**
     * @inheritdoc
     */
    function accountExists($data, $userId)
    {
        return $this->userRepository->resultExistsByUserName($data, $userId) === false;
    }
}