<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class ChangePasswordValidationRule extends PasswordValidationRule
{
    protected function checkPassword($password, $passwordConfirmation)
    {
        return !(!empty($password) && !empty($passwordConfirmation) && $password !== $passwordConfirmation);
    }
}
