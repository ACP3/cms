<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Validation;

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
            )
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'sidebar',
                    'message' => $this->translator->t('system', 'select_sidebar_entries'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NumberGreaterThanValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'readmore_chars',
                    'message' => $this->translator->t('news', 'type_in_readmore_chars'),
                    'extra' => [
                        'value' => 0,
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NumberGreaterThanValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'readmore',
                    'message' => $this->translator->t('news', 'select_activate_readmore'),
                    'extra' => [
                        'haystack' => [0, 1],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'category_in_breadcrumb',
                    'message' => $this->translator->t('news', 'select_display_category_in_breadcrumb'),
                    'extra' => [
                        'haystack' => [0, 1],
                    ],
                ]
            );

        $this->validator->dispatchValidationEvent(
            'news.validation.admin_settings',
            $formData
        );

        $this->validator->validate();
    }
}
