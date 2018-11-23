<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class MinLengthValidationRule extends AbstractValidationRule
{
    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkMinLength($data, $extra['length'] ?? 1);
    }

    /**
     * @param string $value
     * @param int    $length
     *
     * @return bool
     */
    protected function checkMinLength($value, $length)
    {
        return \mb_strlen(\trim($value)) >= $length;
    }
}
