<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Installer\Validation\ValidationRules\ConfigFileValidationRule;
use ACP3\Modules\ACP3\Installer\Validation\ValidationRules\DatabaseConnectionValidationRule;
use ACP3\Modules\ACP3\Installer\Validation\ValidationRules\DesignExistsValidationRule;

class FormValidation extends Core\Validation\AbstractFormValidation
{
    private string $configFilePath = '';

    /**
     * @return $this
     */
    public function setConfigFilePath(string $configFilePath): self
    {
        $this->configFilePath = $configFilePath;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'db_host',
                    'message' => $this->translator->t('installer', 'type_in_db_host'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'db_user',
                    'message' => $this->translator->t('installer', 'type_in_db_username'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'db_name',
                    'message' => $this->translator->t('installer', 'type_in_db_name'),
                ]
            )
            ->addConstraint(
                DatabaseConnectionValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['db_host', 'db_user', 'db_password', 'db_name'],
                    'message' => $this->translator->t('installer', 'db_connection_failed'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'user_name',
                    'message' => $this->translator->t('installer', 'type_in_user_name'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('installer', 'wrong_email_format'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'date_format_long',
                    'message' => $this->translator->t('installer', 'type_in_long_date_format'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'date_format_short',
                    'message' => $this->translator->t('installer', 'type_in_short_date_format'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\PasswordValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['user_pwd', 'user_pwd_wdh'],
                    'message' => $this->translator->t('installer', 'type_in_pwd'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\TimeZoneExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'date_time_zone',
                    'message' => $this->translator->t('installer', 'select_time_zone'),
                ]
            )
            ->addConstraint(
                ConfigFileValidationRule::class,
                [
                    'data' => $this->configFilePath,
                    'message' => $this->translator->t('installer', 'wrong_chmod_for_config_file'),
                ]
            )
            ->addConstraint(
                DesignExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'design',
                    'message' => $this->translator->t('installer', 'select_valid_design'),
                ]
            );

        $this->validator->validate();
    }
}
