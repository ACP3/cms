<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Validation\ValidationRules\PermissionsExistValidationRule;
use ACP3\Modules\ACP3\Permissions\Validation\ValidationRules\RoleNotExistsValidationRule;

class RoleFormValidation extends Core\Validation\AbstractFormValidation
{
    private int $roleId = 0;

    /**
     * @return $this
     */
    public function setRoleId(int $roleId): self
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'name',
                    'message' => $this->translator->t('system', 'name_to_short'),
                ]
            )
            ->addConstraint(
                RoleNotExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'name',
                    'message' => $this->translator->t('permissions', 'role_already_exists'),
                    'extra' => [
                        'role_id' => $this->roleId,
                    ],
                ]
            )
            ->addConstraint(
                PermissionsExistValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'resources',
                    'message' => $this->translator->t('permissions', 'invalid_resource_or_permission'),
                ]
            );

        $this->validator->validate();
    }
}
