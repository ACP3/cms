<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Validation;

use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaTypeValidationRule;

class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Validation\Exceptions\InvalidFormTokenException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(FormTokenValidationRule::class)
            ->addConstraint(CaptchaTypeValidationRule::class, [
                'data' => $formData,
                'field' => 'captcha',
                'message' => $this->translator->t('captcha', 'select_captcha_type'),
            ]);

        $this->validator->dispatchValidationEvent('captcha.validation.admin_settings.custom_fields', $formData);

        $this->validator->validate();
    }
}
