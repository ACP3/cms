<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class MinLengthValidationRule extends AbstractValidationRule
{
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkMinLength((string) $data, $extra['length'] ?? 1);
    }

    private function checkMinLength(string $value, int $length): bool
    {
        return mb_strlen(trim($value)) >= $length;
    }
}
