<?php
namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Core\Validation\ValidationRules\BirthdayValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByEmailValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByNameValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\IcqNumberValidationRule;

abstract class AbstractUserFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @param array  $formData
     * @param string $passwordField
     * @param string $passwordConfirmationField
     */
    protected function validateNewPassword(array $formData, $passwordField, $passwordConfirmationField)
    {
        $this->validator->addConstraint(
            Core\Validation\ValidationRules\ChangePasswordValidationRule::class,
            [
                'data' => $formData,
                'field' => [$passwordField, $passwordConfirmationField],
                'message' => $this->translator->t('users', 'type_in_pwd')
            ]
        );
    }

    /**
     * @param array  $formData
     * @param string $passwordField
     * @param string $passwordConfirmationField
     */
    protected function validatePassword(array $formData, $passwordField, $passwordConfirmationField)
    {
        $this->validator->addConstraint(
            Core\Validation\ValidationRules\PasswordValidationRule::class,
            [
                'data' => $formData,
                'field' => [$passwordField, $passwordConfirmationField],
                'message' => $this->translator->t('users', 'type_in_pwd')
            ]
        );
    }

    /**
     * @param array $formData
     * @param int   $userId
     */
    protected function validateAccountCoreData(array $formData, $userId)
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->translator->t('system', 'name_to_short')
                ]
            )
            ->addConstraint(
                AccountNotExistsByNameValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->translator->t('users', 'user_name_already_exists'),
                    'extra' => [
                        'user_id' => $userId
                    ]
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'gender',
                    'message' => $this->translator->t('users', 'select_gender'),
                    'extra' => [
                        'haystack' => [1, 2, 3]
                    ]
                ]
            )
            ->addConstraint(
                BirthdayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'birthday',
                    'message' => $this->translator->t('users', 'invalid_birthday')
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('system', 'wrong_email_format')
                ]
            )
            ->addConstraint(
                AccountNotExistsByEmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('users', 'user_email_already_exists'),
                    'extra' => [
                        'user_id' => $userId
                    ]
                ]
            )
            ->addConstraint(
                IcqNumberValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'icq',
                    'message' => $this->translator->t('users', 'invalid_icq_number')
                ]
            );
    }

    /**
     * @param array $formData
     */
    protected function validateUserSettings(array $formData)
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail_display',
                    'message' => $this->translator->t('users', 'select_mail_display'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'address_display',
                    'message' => $this->translator->t('users', 'select_address_display'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'country_display',
                    'message' => $this->translator->t('users', 'select_country_display'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'birthday_display',
                    'message' => $this->translator->t('users', 'select_birthday_display'),
                    'extra' => [
                        'haystack' => [0, 1, 2]
                    ]
                ]
            );
    }
}
