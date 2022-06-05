<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserNameValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly IntegerValidationRule $integerValidationRule)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \is_array($field)) {
            $userName = reset($field);
            $userId = next($field);

            return (!empty($data[$userId]) && $this->integerValidationRule->isValid($data[$userId])) || !empty($data[$userName]);
        }

        return false;
    }
}
