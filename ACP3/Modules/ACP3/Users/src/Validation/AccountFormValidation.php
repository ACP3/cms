<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;

class AccountFormValidation extends AbstractUserFormValidation
{
    protected int $userId = 0;

    /**
     * @return $this
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
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
