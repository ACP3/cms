<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

class IsExternalFileValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \is_array($field)) {
            $external = \reset($field);
            $filesize = \next($field);
            $unit = \next($field);

            $file = $extra['file'] ?? null;

            return !(isset($data[$external]) && (empty($file) || empty($data[$filesize]) || empty($data[$unit])));
        }

        return false;
    }
}
