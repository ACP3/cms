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
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withRoleId instead.
     */
    public function setRoleId(int $roleId): static
    {
        $this->roleId = $roleId;

        return $this;
    }

    public function withRoleId(int $roleId): static
    {
        $clone = clone $this;
        $clone->roleId = $roleId;

        return $clone;
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
