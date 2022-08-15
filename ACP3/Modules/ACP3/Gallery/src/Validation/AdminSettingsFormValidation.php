<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Validation;

use ACP3\Core;

class AdminSettingsFormValidation extends Core\Validation\AbstractFormValidation
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
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'thumbwidth',
                    'message' => $this->translator->t('gallery', 'invalid_image_width_entered'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'width',
                    'message' => $this->translator->t('gallery', 'invalid_image_width_entered'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'thumbheight',
                    'message' => $this->translator->t('gallery', 'invalid_image_height_entered'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'height',
                    'message' => $this->translator->t('gallery', 'invalid_image_height_entered'),
                ]
            );

        $this->validator->dispatchValidationEvent(
            'gallery.validation.admin_settings',
            $formData
        );

        $this->validator->validate();
    }
}
