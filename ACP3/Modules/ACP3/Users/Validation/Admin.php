<?php
namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Validation\ValidationRules\RolesExistValidationRule;

/**
 * Class Admin
 * @package ACP3\Modules\ACP3\Users\Validator
 */
class Admin extends AbstractUserFormValidation
{
    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'language_override',
                    'message' => $this->translator->t('users', 'select_languages_override')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'entries_override',
                    'message' => $this->translator->t('users', 'select_entries_override')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'enable_registration',
                    'message' => $this->translator->t('users', 'select_enable_registration')
                ]);

        $this->validator->validate();
    }

    /**
     * @param array $formData
     * @param int   $userId
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $userId = 0)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                RolesExistValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'roles',
                    'message' => $this->translator->t('users', 'select_access_level')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'super_user',
                    'message' => $this->translator->t('users', 'select_super_user'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]);

        $this->validateAccountCoreData($formData, $userId);
        $this->validateUserSettings($formData, 1, 1);

        if (isset($formData['new_pwd'])) {
            $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');
        } else {
            $this->validatePassword($formData, 'pwd', 'pwd_repeat');
        }

        $this->validator->validate();
    }
}
