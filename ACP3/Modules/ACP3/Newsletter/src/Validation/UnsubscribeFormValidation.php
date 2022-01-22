<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountExistsValidationRule;

class UnsubscribeFormValidation extends AbstractFormValidation
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('system', 'wrong_email_format'),
                ]
            )
            ->addConstraint(
                AccountExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('newsletter', 'account_not_exists'),
                ]
            );

        $this->validator->dispatchValidationEvent('captcha.validation.validate_captcha', $formData);

        $this->validator->validate();
    }
}
