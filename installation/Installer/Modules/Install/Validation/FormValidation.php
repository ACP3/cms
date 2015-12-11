<?php
namespace ACP3\Installer\Modules\Install\Validation;

use ACP3\Core;
use ACP3\Installer\Modules\Install\Validation\ValidationRules\ConfigFileValidationRule;
use ACP3\Installer\Modules\Install\Validation\ValidationRules\DatabaseConnectionValidationRule;

/**
 * Class Validator
 * @package ACP3\Installer\Modules\Install\Validation
 */
class FormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @param array  $formData
     * @param string $configFilePath
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateConfiguration(array $formData, $configFilePath)
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'db_host',
                    'message' => $this->translator->t('install', 'type_in_db_host')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'db_user',
                    'message' => $this->translator->t('install', 'type_in_db_username')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'db_name',
                    'message' => $this->translator->t('install', 'type_in_db_name')
                ])
            ->addConstraint(
                DatabaseConnectionValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['db_host', 'db_user', 'db_password', 'db_name'],
                    'message' => $this->translator->t('install', 'db_connection_failed')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'user_name',
                    'message' => $this->translator->t('install', 'type_in_user_name')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('install', 'wrong_email_format')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'date_format_long',
                    'message' => $this->translator->t('install', 'type_in_long_date_format')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'date_format_short',
                    'message' => $this->translator->t('install', 'type_in_short_date_format')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\PasswordValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['user_pwd', 'user_pwd_wdh'],
                    'message' => $this->translator->t('install', 'type_in_pwd')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\TimeZoneExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'date_time_zone',
                    'message' => $this->translator->t('install', 'select_time_zone')
                ])
            ->addConstraint(
                ConfigFileValidationRule::NAME,
                [
                    'data' => $configFilePath,
                    'message' => $this->translator->t('install', 'wrong_chmod_for_config_file')
                ]);

        $this->validator->validate();
    }
}
