<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class ChangePasswordValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class ChangePasswordValidationRule extends PasswordValidationRule
{
    /**
     * @inheritdoc
     */
    protected function checkPassword($password, $passwordConfirmation)
    {
        return !(!empty($password) && !empty($passwordConfirmation) && $password !== $passwordConfirmation);
    }
}