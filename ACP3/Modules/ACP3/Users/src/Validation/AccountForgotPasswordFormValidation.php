<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountExistsByEmailValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountExistsByNameValidationRule;

class AccountForgotPasswordFormValidation extends AbstractFormValidation
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        if ($this->validator->is(Core\Validation\ValidationRules\EmailValidationRule::class, $formData['nick_mail'])) {
            $ruleName = AccountExistsByEmailValidationRule::class;
        } else {
            $ruleName = AccountExistsByNameValidationRule::class;
        }

        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'nick_mail',
                    'message' => $this->translator->t('users', 'type_in_nickname_or_email'),
                ]
            )
            ->addConstraint(
                $ruleName,
                [
                    'data' => $formData,
                    'field' => 'nick_mail',
                    'message' => $this->translator->t('users', 'user_not_exists'),
                ]
            );

        $this->validator->dispatchValidationEvent('captcha.validation.validate_captcha', $formData);

        $this->validator->validate();
    }
}
