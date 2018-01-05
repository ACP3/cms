<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation\ValidationRules;

class AccountNotExistsByEmailValidationRule extends AbstractAccountNotExistsValidationRule
{
    /**
     * @inheritdoc
     */
    protected function accountExists($data, $userId)
    {
        return $this->userRepository->resultExistsByEmail($data, $userId) === false;
    }
}
