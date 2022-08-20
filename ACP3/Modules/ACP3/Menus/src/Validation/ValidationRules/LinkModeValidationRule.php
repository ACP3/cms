<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Modules;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validation\ValidationRules\InternalUriValidationRule;
use ACP3\Modules\ACP3\Menus\Enum\PageTypeEnum;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LinkModeValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly Modules $modules, private readonly InternalUriValidationRule $internalUriValidationRule)
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

    private function isValidLink(int $mode, string $moduleName, string $uri): bool
    {
        return match ($mode) {
            PageTypeEnum::MODULE->value => $this->modules->isInstalled($moduleName),
            PageTypeEnum::DYNAMIC_PAGE->value => $this->internalUriValidationRule->isValid($uri),
            PageTypeEnum::HYPERLINK->value => !empty($uri),
            default => false,
        };
    }
}
