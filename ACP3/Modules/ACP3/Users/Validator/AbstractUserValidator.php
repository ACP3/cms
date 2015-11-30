<?php
namespace ACP3\Modules\ACP3\Users\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Users\Validator\ValidationRules\AccountNotExistsByEmailValidationRule;
use ACP3\Modules\ACP3\Users\Validator\ValidationRules\AccountNotExistsByNameValidationRule;
use ACP3\Core\Validator\ValidationRules\BirthdayValidationRule;
use ACP3\Modules\ACP3\Users\Validator\ValidationRules\IcqNumberValidationRule;

/**
 * Class AbstractUserValidator
 * @package ACP3\Modules\ACP3\Users\Validator
 */
abstract class AbstractUserValidator extends Core\Validator\AbstractValidator
{
    /**
     * @param array  $formData
     * @param string $passwordField
     * @param string $passwordConfirmationField
     */
    protected function validateNewPassword(array $formData, $passwordField, $passwordConfirmationField)
    {
        $this->validator->addConstraint(
            Core\Validator\ValidationRules\ChangePasswordValidationRule::NAME,
            [
                'data' => $formData,
                'field' => [$passwordField, $passwordConfirmationField],
                'message' => $this->lang->t('users', 'type_in_pwd')
            ]);
    }

    /**
     * @param array  $formData
     * @param string $passwordField
     * @param string $passwordConfirmationField
     */
    protected function validatePassword(array $formData, $passwordField, $passwordConfirmationField)
    {
        $this->validator->addConstraint(
            Core\Validator\ValidationRules\PasswordValidationRule::NAME,
            [
                'data' => $formData,
                'field' => [$passwordField, $passwordConfirmationField],
                'message' => $this->lang->t('users', 'type_in_pwd')
            ]);
    }

    /**
     * @param array $formData
     * @param int   $userId
     */
    protected function validateAccountCoreData(array $formData, $userId)
    {
        $this->validator
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->lang->t('system', 'name_to_short')
                ])
            ->addConstraint(
                AccountNotExistsByNameValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->lang->t('users', 'user_name_already_exists'),
                    'extra' => [
                        'user_id' => $userId
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'gender',
                    'message' => $this->lang->t('users', 'select_gender'),
                    'extra' => [
                        'haystack' => [1, 2, 3]
                    ]
                ])
            ->addConstraint(
                BirthdayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'birthday',
                    'message' => $this->lang->t('users', 'invalid_birthday')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                AccountNotExistsByEmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('users', 'user_email_already_exists'),
                    'extra' => [
                        'user_id' => $userId
                    ]
                ])
            ->addConstraint(
                IcqNumberValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'icq',
                    'message' => $this->lang->t('users', 'invalid_icq_number')
                ]);
    }

    /**
     * @param array $formData
     * @param int   $languageOverride
     * @param int   $entriesOverride
     */
    protected function validateUserSettings(array $formData, $languageOverride = 1, $entriesOverride = 1)
    {
        if ($languageOverride == 1) {
            $this->validator->addConstraint(
                Core\Validator\ValidationRules\LanguagePackExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'language',
                    'message' => $this->lang->t('users', 'select_language')
                ]);
        }
        if ($entriesOverride == 1) {
            $this->validator->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'entries',
                    'message' => $this->lang->t('system', 'select_records_per_page')
                ]);
        }

        $this->validator
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'date_format_long',
                    'message' => $this->lang->t('system', 'type_in_long_date_format')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'date_format_short',
                    'message' => $this->lang->t('system', 'type_in_short_date_format')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\TimeZoneExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'date_time_zone',
                    'messgae' => $this->lang->t('system', 'select_time_zone')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail_display',
                    'message' => $this->lang->t('users', 'select_mail_display'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'address_display',
                    'message' => $this->lang->t('users', 'select_address_display'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'country_display',
                    'message' => $this->lang->t('users', 'select_country_display'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'birthday_display',
                    'message' => $this->lang->t('users', 'select_birthday_display'),
                    'extra' => [
                        'haystack' => [0, 1, 2]
                    ]
                ]);
    }
}