<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;

class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function validate(array $formData)
    {
        $this->validator->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class);

        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'enabled',
                    'message' => $this->translator->t('cookieconsent', 'select_enable_cookie_consent'),
                    'extra' => [
                        'haystack' => [0, 1],
                    ],
                ]
            );

        $this->validator->validate();
    }
}
