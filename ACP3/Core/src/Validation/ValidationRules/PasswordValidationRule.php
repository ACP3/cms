<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PasswordValidationRule extends AbstractValidationRule
{
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \is_array($field)) {
            $password = reset($field);
            $passwordConfirmation = next($field);

            if ($password !== false && $passwordConfirmation !== false) {
                return $this->checkPassword($data[$password], $data[$passwordConfirmation]);
            }
        }

        return false;
    }

    /**
     * @param string $password
     * @param string $passwordConfirmation
     *
     * @return bool
     */
    protected function checkPassword($password, $passwordConfirmation)
    {
        return !empty($password) && $password === $passwordConfirmation;
    }
}
