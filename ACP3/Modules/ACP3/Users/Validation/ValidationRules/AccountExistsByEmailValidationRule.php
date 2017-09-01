<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation\ValidationRules;

class AccountExistsByEmailValidationRule extends AccountNotExistsByEmailValidationRule
{
    /**
     * @inheritdoc
     */
    protected function accountExists($data, $userId)
    {
        return !parent::accountExists($data, $userId);
    }
}
