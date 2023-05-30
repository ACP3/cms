<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class BirthdayValidationRule extends AbstractValidationRule
{
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return empty($data) || $this->isBirthday($data);
    }

    protected function isBirthday(string $value): bool
    {
        $regex = '/^(\d{4})-(\d{2})-(\d{2})$/';
        $matches = [];

        return preg_match($regex, $value, $matches) && checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }
}
