<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;

class AccountFormValidation extends AbstractUserFormValidation
{
    protected $userId = 0;

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = (int)$userId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class);

        $this->validateAccountCoreData($formData, $this->userId);

        $this->validator->validate();
    }
}
