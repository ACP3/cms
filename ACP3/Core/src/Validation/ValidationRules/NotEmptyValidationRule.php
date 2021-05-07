<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class NotEmptyValidationRule extends AbstractValidationRule
{
    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_scalar($data)) {
            return !empty(trim($data));
        }

        if (\is_array($data)) {
            if (empty($field)) {
                return \count($data) > 0;
            }

            if (\array_key_exists($field, $data)) {
                return !empty(is_scalar($data[$field]) ? trim($data[$field]) : $data[$field]);
            }
        }

        return false;
    }
}
