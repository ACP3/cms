<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Users\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractAccountNotExistsValidationRule extends AbstractValidationRule
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->accountExists($data, $extra['user_id'] ?? 0);
    }

    abstract protected function accountExists(string $data, int $userId): bool;
}
