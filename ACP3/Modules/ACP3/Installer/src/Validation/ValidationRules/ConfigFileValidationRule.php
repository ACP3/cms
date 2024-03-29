<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ConfigFileValidationRule extends AbstractValidationRule
{
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        return is_file($data) === true && is_writable($data) === true;
    }
}
