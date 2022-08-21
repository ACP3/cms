<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;

class AccountFormValidation extends AbstractUserFormValidation
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

    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        $this->validator->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class);

        $this->validateAccountCoreData($formData, $this->userId);

        $this->validator->validate();
    }
}
