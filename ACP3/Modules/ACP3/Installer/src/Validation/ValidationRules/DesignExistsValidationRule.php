<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Validation\ValidationRules;

use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

class DesignExistsValidationRule extends AbstractValidationRule
{
    public function __construct(private ThemePathInterface $theme)
    {
    }

    /**
     * @param mixed  $data
     * @param string $field
     *
     * @return bool
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->theme->has($data);
    }
}
