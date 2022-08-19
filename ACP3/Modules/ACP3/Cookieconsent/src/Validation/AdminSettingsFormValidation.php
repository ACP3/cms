<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\Validation;

use ACP3\Core\Helpers\Enum\YesNoEnum;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;

class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function validate(array $formData): void
    {
        $this->validator->addConstraint(FormTokenValidationRule::class);

        $this->validator
            ->addConstraint(
                InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'enabled',
                    'message' => $this->translator->t('cookieconsent', 'select_enable_cookie_consent'),
                    'extra' => [
                        'haystack' => YesNoEnum::values(),
                    ],
                ]
            );

        $this->validator->validate();
    }
}
