<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Modules\ACP3\Seo\Helper\Enum\IndexPaginatedContentEnum;

class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'robots',
                    'message' => $this->translator->t('seo', 'select_robots'),
                    'extra' => [
                        'haystack' => [1, 2, 3, 4],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'index_paginated_content',
                    'message' => $this->translator->t('seo', 'select_index_paginated_content'),
                    'extra' => [
                        'haystack' => IndexPaginatedContentEnum::cases(),
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'sitemap_is_enabled',
                    'message' => $this->translator->t('seo', 'select_sitemap_is_enabled'),
                    'extra' => [
                        'haystack' => [0, 1],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'sitemap_save_mode',
                    'message' => $this->translator->t('seo', 'select_sitemap_save_mode'),
                    'extra' => [
                        'haystack' => [1, 2],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'sitemap_separate',
                    'message' => $this->translator->t('seo', 'select_sitemap_separate'),
                    'extra' => [
                        'haystack' => [0, 1],
                    ],
                ]
            );

        $this->validator->validate();
    }
}
