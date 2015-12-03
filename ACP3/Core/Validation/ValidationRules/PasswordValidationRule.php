<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class PasswordValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class PasswordValidationRule extends AbstractValidationRule
{
    const NAME = 'password';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && is_array($field)) {
            $password = reset($field);
            $passwordConfirmation = next($field);

            return $this->checkPassword($data[$password], $data[$passwordConfirmation]);
        }

        return false;
    }

    /**
     * @param string $password
     * @param string $passwordConfirmation
     *
     * @return bool
     */
    protected function checkPassword($password, $passwordConfirmation)
    {
        return !empty($password) && $password === $passwordConfirmation;
    }
}