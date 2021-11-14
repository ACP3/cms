<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Newsletter\Repository\AccountRepository;

class AccountExistsValidationRule extends AbstractValidationRule
{
    public function __construct(private AccountRepository $accountRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkAccountExists($data);
    }

    protected function checkAccountExists(string $data): bool
    {
        return $this->accountRepository->accountExists($data);
    }
}
