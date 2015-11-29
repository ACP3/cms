<?php
namespace ACP3\Modules\ACP3\Users\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Validator\ValidationRules\RolesExistValidationRule;

/**
 * Class Admin
 * @package ACP3\Modules\ACP3\Users\Validator
 */
class Admin extends AbstractUserValidator
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
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'language_override',
                    'message' => $this->lang->t('users', 'select_languages_override')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'entries_override',
                    'message' => $this->lang->t('users', 'select_entries_override')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'enable_registration',
                    'message' => $this->lang->t('users', 'select_enable_registration')
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
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                RolesExistValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'roles',
                    'message' => $this->lang->t('users', 'select_access_level')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'super_user',
                    'message' => $this->lang->t('users', 'select_super_user'),
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
