<?php

namespace ACP3\Modules\ACP3\Users\Validator;

use ACP3\Core;

/**
 * Class Account
 * @package ACP3\Modules\ACP3\Users\Validator
 */
class Account extends AbstractUserValidator
{
    /**
     * @param array $formData
     * @param int   $userId
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEditProfile(array $formData, $userId)
    {
        $this->validator->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME);;

        $this->validateAccountCoreData($formData, $userId);
        $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');

        $this->validator->validate();
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateUserSettings(array $formData, array $settings)
    {
        $this->validator->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME);

        parent::validateUserSettings($formData, $settings['language_override'], $settings['entries_override']);

        $this->validator->validate();
    }

}