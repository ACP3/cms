<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Validation\ValidationRules\PrivilegeExistsValidationRule;

class ResourceFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $formData)
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
                            Core\Controller\AreaEnum::AREA_ADMIN,
                            Core\Controller\AreaEnum::AREA_FRONTEND,
                            Core\Controller\AreaEnum::AREA_WIDGET,
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
                PrivilegeExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'privileges',
                    'message' => $this->translator->t('permissions', 'privilege_does_not_exist'),
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
