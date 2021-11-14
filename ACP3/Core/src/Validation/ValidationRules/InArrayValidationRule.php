<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class InArrayValidationRule extends AbstractValidationRule
{
    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data) && !\is_array($data[$field])) {
            return $this->isValid($data[$field], $field, $extra);
        }

        if (empty($extra['haystack']) || \is_array($extra['haystack']) === false) {
            return false;
        }

        return $this->checkInArray($data, $field, $extra['haystack']);
    }

    protected function checkInArray(array|string $data, string $field, array $haystack): bool
    {
        if (isset($data[$field]) && \is_array($data[$field])) {
            foreach ($data[$field] as $row) {
                if (\in_array($row, $haystack, false) === false) {
                    return false;
                }
            }

            return true;
        }

        return \in_array($data, $haystack, false);
    }
}
