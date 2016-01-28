<?php
namespace ACP3\Modules\ACP3\Users\Validation\ValidationRules;

/**
 * Class AccountNotNotExistsByNameValidationRule
 * @package ACP3\Modules\ACP3\Users\Validation\ValidationRules
 */
class AccountNotExistsByNameValidationRule extends AbstractAccountNotExistsValidationRule
{
    /**
     * @inheritdoc
     */
    protected function accountExists($data, $userId)
    {
        return $this->userRepository->resultExistsByUserName($data, $userId) === false;
    }
}