<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Validation\ValidationRules;

use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DesignExistsValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly ThemePathInterface $theme)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->theme->has($data);
    }
}
