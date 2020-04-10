<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;

class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'dateformat',
                    'message' => $this->translator->t('system', 'select_date_format'),
                    'extra' => [
                        'haystack' => ['long', 'short'],
                    ],
                ]
            );

        $this->validator->validate();
    }
}
