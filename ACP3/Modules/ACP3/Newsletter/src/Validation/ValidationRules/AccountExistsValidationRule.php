<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Newsletter\Repository\AccountRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AccountExistsValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly AccountRepository $accountRepository)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
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
