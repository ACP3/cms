<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Validation;

use ACP3\Core;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'feed_type',
                    'message' => $this->translator->t('feeds', 'select_feed_type'),
                    'extra' => [
                        'haystack' => ['RSS 1.0', 'RSS 2.0', 'ATOM'],
                    ],
                ]
            );

        $this->validator->validate();
    }
}
