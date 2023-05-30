<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Core\Helpers\Enum\YesNoEnum;
use ACP3\Modules\ACP3\Permissions\Validation\ValidationRules\RolesExistValidationRule;

class AdminFormValidation extends AbstractUserFormValidation
{
    private int $userId = 0;

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withUserId instead.
     */
    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function withUserId(int $userId): static
    {
        $clone = clone $this;
        $clone->userId = $userId;

        return $clone;
    }

    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                RolesExistValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'roles',
                    'message' => $this->translator->t('users', 'select_access_level'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'super_user',
                    'message' => $this->translator->t('users', 'select_super_user'),
                    'extra' => [
                        'haystack' => YesNoEnum::values(),
                    ],
                ]
            );

        $this->validateAccountCoreData($formData, $this->userId);
        $this->validateUserSettings($formData);

        if (isset($formData['new_pwd'])) {
            $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');
        } else {
            $this->validatePassword($formData, 'pwd', 'pwd_repeat');
        }

        $this->validator->validate();
    }
}
