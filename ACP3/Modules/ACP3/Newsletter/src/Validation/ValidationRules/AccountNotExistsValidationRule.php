<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules;

class AccountNotExistsValidationRule extends AccountExistsValidationRule
{
    /**
     * {@inheritdoc}
     */
    protected function checkAccountExists(string $data): bool
    {
        return !parent::checkAccountExists($data);
    }
}
