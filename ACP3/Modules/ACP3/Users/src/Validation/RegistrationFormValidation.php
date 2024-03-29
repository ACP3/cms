<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByEmailValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByNameValidationRule;

class RegistrationFormValidation extends AbstractUserFormValidation
{
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->translator->t('system', 'name_to_short'),
                ]
            )
            ->addConstraint(
                AccountNotExistsByNameValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->translator->t('users', 'user_name_already_exists'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('system', 'wrong_email_format'),
                ]
            )
            ->addConstraint(
                AccountNotExistsByEmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('users', 'user_email_already_exists'),
                ]
            );

        $this->validatePassword($formData, 'pwd', 'pwd_repeat');

        $this->validator->dispatchValidationEvent('captcha.validation.validate_captcha', $formData);

        $this->validator->validate();
    }
}
