<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;

class AccountSettingsFormValidation extends AbstractUserFormValidation
{
    public function validate(array $formData): void
    {
        $this->validator->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class);

        $this->validateUserSettings($formData);
        $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');

        $this->validator->validate();
    }
}
