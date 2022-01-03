<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Modules;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validation\ValidationRules\InternalUriValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LinkModeValidationRule extends AbstractValidationRule
{
    public function __construct(protected Modules $modules, protected InternalUriValidationRule $internalUriValidationRule)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
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
        return match ($mode) {
            1 => $this->modules->isInstalled($moduleName),
            2 => $this->internalUriValidationRule->isValid($uri),
            3 => !empty($uri),
            default => false,
        };
    }
}
