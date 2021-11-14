<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class DateValidationRule extends AbstractValidationRule
{
    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data)) {
            if (\is_array($field)) {
                $start = reset($field);
                $end = next($field);

                return $this->checkIsValidDate($data[$start], $data[$end]);
            }

            if (!empty($field)) {
                return $this->isValid($data[$field], $field, $extra);
            }

            return $this->checkIsValidDate(reset($data), next($data));
        }

        return $this->checkIsValidDate($data);
    }

    protected function checkIsValidDate(string $start, ?string $end = null): bool
    {
        if ($this->matchIsDate($start)) {
            // Check date range
            if ($end !== null && $this->matchIsDate($end)) {
                return strtotime($start) <= strtotime($end);
            }

            return true;
        }

        return false;
    }

    protected function matchIsDate(string $date): bool
    {
        $pattern = '/^(\d{4})-(\d{2})-(\d{2})(( |T)([01]\d|2[0-3])(:([0-5]\d)){1,2})?$/';

        return preg_match($pattern, $date, $matches) && checkdate($matches[2], $matches[3], $matches[1]);
    }
}
