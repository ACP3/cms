<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Validation;

use ACP3\Core;

class ResourceFormValidation extends Core\Validation\AbstractFormValidation
{
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\ModuleIsInstalledValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'modules',
                    'message' => $this->translator->t('permissions', 'select_module'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'area',
                    'message' => $this->translator->t('permissions', 'type_in_area'),
                    'extra' => [
                        'haystack' => [
                            Core\Controller\AreaEnum::AREA_ADMIN->value,
                            Core\Controller\AreaEnum::AREA_FRONTEND->value,
                            Core\Controller\AreaEnum::AREA_WIDGET->value,
                        ],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'controller',
                    'message' => $this->translator->t('permissions', 'type_in_controller'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InternalUriValidationRule::class,
                [
                    'data' => strtolower($formData['modules'] . '/' . $formData['controller'] . '/' . $formData['resource'] . '/'),
                    'field' => 'resource',
                    'message' => $this->translator->t('permissions', 'type_in_resource'),
                ]
            );

        $this->validator->validate();
    }
}
