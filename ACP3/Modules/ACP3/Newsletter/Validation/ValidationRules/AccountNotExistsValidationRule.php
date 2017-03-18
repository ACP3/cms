<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules;

class AccountNotExistsValidationRule extends AccountExistsValidationRule
{
    /**
     * @inheritdoc
     */
    protected function checkAccountExists($data)
    {
        return !parent::checkAccountExists($data);
    }
}
