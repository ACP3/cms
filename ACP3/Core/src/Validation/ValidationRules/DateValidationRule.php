<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class DateValidationRule extends AbstractValidationRule
{
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
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

    protected function checkIsValidDate(string $start, string $end = null): bool
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

        return preg_match($pattern, $date, $matches) && checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }
}
