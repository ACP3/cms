<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Modules;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validation\ValidationRules\InternalUriValidationRule;

class LinkModeValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Validation\ValidationRules\InternalUriValidationRule
     */
    protected $internalUriValidationRule;

    public function __construct(
        Modules $modules,
        InternalUriValidationRule $internalUriValidationRule
    ) {
        $this->modules = $modules;
        $this->internalUriValidationRule = $internalUriValidationRule;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \is_array($field)) {
            $mode = reset($field);
            $moduleName = next($field);
            $uri = next($field);

            return $this->isValidLink((int) $data[$mode], $data[$moduleName], $data[$uri]);
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function isValidLink(int $mode, string $moduleName, string $uri)
    {
        switch ($mode) {
            case 1:
                return $this->modules->isActive($moduleName);
            case 2:
                return $this->internalUriValidationRule->isValid($uri);
            case 3:
                return !empty($uri);
        }

        return false;
    }
}
