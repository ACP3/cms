<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class IcqNumberValidationRule extends AbstractValidationRule
{
    /**
     * {@inheritdoc}
     */
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return !empty($data) ? $this->isIcqNumber($data) : true;
    }

    protected function isIcqNumber(string $value): bool
    {
        return (bool) preg_match('/^(\d{6,9})$/', $value);
    }
}
